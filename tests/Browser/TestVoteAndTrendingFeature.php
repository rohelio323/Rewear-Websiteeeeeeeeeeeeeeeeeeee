<?php

use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Laravel\Dusk\Browser;

function makeVoter(): User
{
    return User::factory()->create();
}

function makePost(User $owner, string $title, int $score = 0, ?string $createdAt = null): Post
{
    $post = Post::create([
        'title'        => $title,
        'content'      => 'Content for: ' . $title,
        'users_id'     => $owner->id,
        'upvote_count' => $score,
    ]);

    if ($createdAt) {
        $post->created_at = $createdAt;
        $post->save();
    }

    return $post;
}

function seedVote(Post $post, User $user, int $value): void
{
    PostVote::firstOrCreate(
        ['post_id' => $post->post_id, 'user_id' => $user->id],
        ['value'   => $value]
    );
    $post->recalculateScore();
    $post->refresh();
}

// ─────────────────────────────────────────────────────────────────────────────
// PBI-26 — Voting Mechanism
// ─────────────────────────────────────────────────────────────────────────────

// TC.Vote.26.001 | Positive — Logged-in user can upvote a post
test('TC.Vote.26.001 UC Positive: logged-in user upvotes a post and score increases', function () {
    $user = makeVoter();
    $post = makePost($user, 'TC.Vote.26.001 Upvote Test ' . uniqid(), 0);

    $this->browse(function (Browser $browser) use ($user, $post) {
        $browser->loginAs($user)
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(800); // wait for Alpine.js to boot

        // Click the upvote button (first button inside [x-data]) on the first (newest) article
        $browser->click('article:first-of-type [x-data] button:first-child')
                ->pause(1500); // wait for AJAX to resolve

        // Score should now be 1
        $browser->assertSeeIn('article:first-of-type [x-data]', '1');
    });
});

// TC.Vote.26.002 | Negative — Guest cannot vote and sees no vote buttons
test('TC.Vote.26.002 UC Negative: guest sees no upvote button and cannot vote', function () {
    $owner = makeVoter();
    $post  = makePost($owner, 'TC.Vote.26.002 Guest Vote Test ' . uniqid(), 0);

    $this->browse(function (Browser $browser) use ($post) {
        $browser->logout()
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(500);

        $browser->assertMissing('[x-data]');
    });
});

// TC.Vote.26.003 | Positive — Logged-in user can downvote a post
test('TC.Vote.26.003 UC Positive: logged-in user downvotes a post and score decreases', function () {
    $user = makeVoter();
    $post = makePost($user, 'TC.Vote.26.003 Downvote Test ' . uniqid(), 0);

    $this->browse(function (Browser $browser) use ($user, $post) {
        $browser->loginAs($user)
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(800);

        // Click the downvote button (last button inside [x-data]) on the first (newest) article
        $browser->click('article:first-of-type [x-data] button:last-child')
                ->pause(1500);

        // Score should now be -1
        $browser->assertSeeIn('article:first-of-type [x-data]', '-1');
    });
});

// TC.Vote.26.004 | Positive — Clicking the same vote twice removes the vote (toggle off)
test('TC.Vote.26.004 EP Positive: clicking same vote again cancels it and score returns to 0', function () {
    $user = makeVoter();
    $post = makePost($user, 'TC.Vote.26.004 Toggle Off Test ' . uniqid(), 0);
    seedVote($post, $user, 1);

    $this->browse(function (Browser $browser) use ($user, $post) {
        $browser->loginAs($user)
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(800);

        // Alpine initialises score from server-rendered upvote_count (= 1)
        $browser->assertSeeIn('article:first-of-type [x-data]', '1');

        // Clicking upvote again (same value) → toggles off → score = 0
        $browser->click('article:first-of-type [x-data] button:first-child')
                ->pause(1500);

        $browser->assertSeeIn('article:first-of-type [x-data]', '0');
    });
});

// TC.Vote.26.005 | Positive — User can switch their vote from upvote to downvote
test('TC.Vote.26.005 EP Positive: user can switch from upvote to downvote', function () {
    $user = makeVoter();
    $post = makePost($user, 'TC.Vote.26.005 Switch Vote Test ' . uniqid(), 0);
    // Pre-seed upvote → score = 1
    seedVote($post, $user, 1);

    $this->browse(function (Browser $browser) use ($user, $post) {
        $browser->loginAs($user)
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(800);

        // Score starts at 1 (pre-seeded upvote)
        $browser->assertSeeIn('article:first-of-type [x-data]', '1');

        // Click downvote (value=-1) to switch
        $browser->click('article:first-of-type [x-data] button:last-child')
                ->pause(1500);

        $browser->assertSeeIn('article:first-of-type [x-data]', '-1');
    });
});

// PBI-27 — Vote Counter (Analytics / Score Display)

// TC.Vote.27.001 | Positive — Score displayed on post card matches DB upvote_count
test('TC.Vote.27.001 UC Positive: score displayed on card matches database upvote_count', function () {
    $owner = makeVoter();
    $post  = makePost($owner, 'TC.Vote.27.001 Score Display Test ' . uniqid(), 7);

    $this->browse(function (Browser $browser) use ($owner, $post) {
        $browser->loginAs($owner)
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(800);

        // Score of 7 should be visible in the vote counter of the first (newest) article
        $browser->assertSeeIn('article:first-of-type [x-data]', '7');
    });
});

// TC.Vote.27.002 | Negative — Score shows 0 when upvotes equal downvotes (BVA net-zero)
test('TC.Vote.27.002 BVA Negative: score shows 0 when one upvote and one downvote cancel out', function () {
    $owner  = makeVoter();
    $voter  = makeVoter();
    $post   = makePost($owner, 'TC.Vote.27.002 Net Zero Test ' . uniqid(), 0);
    seedVote($post, $owner, 1);   // +1
    seedVote($post, $voter, -1);  // -1 → net = 0

    $this->browse(function (Browser $browser) use ($owner, $post) {
        $browser->loginAs($owner)
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(800);

        // Net score is 0 — displayed in the vote counter of the first (newest) article
        $browser->assertSeeIn('article:first-of-type [x-data]', '0');
    });
});

// TC.Vote.27.003 | Positive — Post owner can access the vote breakdown endpoint
test('TC.Vote.27.003 UC Positive: post owner can access breakdown endpoint and gets likes/dislikes counts', function () {
    $owner  = makeVoter();
    $voter1 = makeVoter();
    $voter2 = makeVoter();
    $post   = makePost($owner, 'TC.Vote.27.003 Breakdown Test ' . uniqid(), 0);
    seedVote($post, $voter1, 1);   // like
    seedVote($post, $voter2, -1);  // dislike

    $this->browse(function (Browser $browser) use ($owner, $post) {
        $browser->loginAs($owner)
                ->visit('/community/posts/' . $post->post_id . '/breakdown')
                ->assertSee('likes')
                ->assertSee('dislikes')
                ->assertSee('1'); // both likes and dislikes count = 1
    });
});

// TC.Vote.27.004 | Negative — Non-owner does NOT see the Analytics button on a post they don't own
test('TC.Vote.27.004 UC Negative: non-owner does not see the Analytics button on another user post', function () {
    $owner    = makeVoter();
    $nonOwner = makeVoter();
    $post     = makePost($owner, 'TC.Vote.27.004 No Analytics Button ' . uniqid(), 0);

    $this->browse(function (Browser $browser) use ($nonOwner, $post) {
        $browser->loginAs($nonOwner)
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(500);

        $browser->assertSourceMissing('openBreakdownModal(' . $post->post_id . ')');
    });
});

// TC.Vote.27.005 | Positive — Score updates in DOM immediately after upvoting (AJAX / Alpine reactive)
test('TC.Vote.27.005 UC Positive: score counter updates immediately after upvoting without page reload', function () {
    $user = makeVoter();
    $post = makePost($user, 'TC.Vote.27.005 AJAX Score Update ' . uniqid(), 0);

    $this->browse(function (Browser $browser) use ($user, $post) {
        $browser->loginAs($user)
                ->visit('/community')
                ->waitFor('article', 5)
                ->waitForText($post->title)
                ->pause(800);

        $browser->assertSeeIn('article:first-of-type [x-data]', '0');

        $browser->click('article:first-of-type [x-data] button:first-child')
                ->pause(1500);

        $browser->assertSeeIn('article:first-of-type [x-data]', '1');
    });
});

// PBI-28 — Trending Feed

// TC.Vote.28.001 | Positive — Popular sort shows highest-voted THIS-WEEK post first
test('TC.Vote.28.001 Positive: popular sort shows highest-voted post before lower-voted post', function () {
    $user = makeVoter();

    $low  = makePost($user, 'TC.Vote.28.001 Low Score Post '  . uniqid(), 2);
    $high = makePost($user, 'TC.Vote.28.001 High Score Post ' . uniqid(), 10);

    $this->browse(function (Browser $browser) use ($user, $low, $high) {
        $browser->loginAs($user)
                ->visit('/community?sort=popular')
                ->waitFor('article', 5)
                ->waitForText($high->title)
                ->waitForText($low->title)
                ->pause(500);

        $browser->assertSee($high->title)->assertSee($low->title);
        $source = $browser->driver->getPageSource();
        expect(strpos($source, $high->title))->toBeLessThan(strpos($source, $low->title));
    });
});


// TC.Vote.28.002 | Positive — Latest sort shows most recent post first
test('TC.Vote.28.002 EP Positive: latest sort shows newest post at the top of the feed', function () {
    $user    = makeVoter();
    $oldPost = makePost($user, 'TC.Vote.28.002 Older Post ' . uniqid(), 0, now()->subHour()->toDateTimeString());
    $newPost = makePost($user, 'TC.Vote.28.002 Newer Post ' . uniqid(), 0);

    $this->browse(function (Browser $browser) use ($user, $oldPost, $newPost) {
        $browser->loginAs($user)
                ->visit('/community') // default = Latest
                ->waitFor('article', 5)
                ->waitForText($newPost->title)
                ->waitForText($oldPost->title)
                ->pause(500);

        $browser->assertSee($newPost->title)->assertSee($oldPost->title);
        $source = $browser->driver->getPageSource();
        expect(strpos($source, $newPost->title))->toBeLessThan(strpos($source, $oldPost->title));
    });
});

// TC.Vote.28.003 | Negative — Old (last week) post does NOT appear in the popular (trending) feed
test('TC.Vote.28.003 EP Negative: post from last week is excluded from the popular trending feed', function () {
    $user = makeVoter();
    // Old post from 2 weeks ago — excluded from trendingPosts (startOfWeek filter)
    $oldPost = makePost($user, 'TC.Vote.28.003 Old Post ' . uniqid(), 999, now()->subWeeks(2)->toDateTimeString());
    // New post this week — included
    $newPost = makePost($user, 'TC.Vote.28.003 New Post ' . uniqid(), 1);

    $this->browse(function (Browser $browser) use ($user, $oldPost, $newPost) {
        $browser->loginAs($user)
                ->visit('/community?sort=popular')
                ->waitFor('article', 5)
                ->pause(500);

        // New post (this week) MUST appear in the trending feed
        $browser->assertSee($newPost->title);

        // Old post (2 weeks ago) must NOT appear — $trendingPosts filters by startOfWeek()
        $browser->assertDontSee($oldPost->title);
    });
});

// TC.Vote.28.004 | Positive — After receiving more upvotes, a post rises in the trending ranking
test('TC.Vote.28.004 UC Positive: post rises in popular ranking after receiving more upvotes', function () {
    $user  = makeVoter();
    $voter = makeVoter();

    // Both created this week → appear in $trendingPosts
    $postA = makePost($user, 'TC.Vote.28.004 Post A Score 5 ' . uniqid(), 5);
    $postB = makePost($user, 'TC.Vote.28.004 Post B Score 1 ' . uniqid(), 1);

    $this->browse(function (Browser $browser) use ($user, $voter, $postA, $postB) {
        // Step 1 — verify A is above B before voting
        $browser->loginAs($user)
                ->visit('/community?sort=popular')
                ->waitFor('article', 5)
                ->waitForText($postA->title)
                ->waitForText($postB->title)
                ->pause(500);

        $browser->assertSee($postA->title)->assertSee($postB->title);
        $source = $browser->driver->getPageSource();
        expect(strpos($source, $postA->title))->toBeLessThan(strpos($source, $postB->title));

        PostVote::create(['post_id' => $postB->post_id, 'user_id' => $voter->id, 'value' => 1]);
        $postB->recalculateScore();
        $extra = User::factory()->count(5)->create();
        foreach ($extra as $v) {
            PostVote::firstOrCreate(
                ['post_id' => $postB->post_id, 'user_id' => $v->id],
                ['value' => 1]
            );
        }
        $postB->recalculateScore();

        $browser->visit('/community?sort=popular')
                ->waitFor('article', 5)
                ->waitForText($postA->title)
                ->waitForText($postB->title)
                ->pause(500);

        $browser->assertSee($postB->title)->assertSee($postA->title);
        $source = $browser->driver->getPageSource();
        expect(strpos($source, $postB->title))->toBeLessThan(strpos($source, $postA->title));
    });
});

// TC.Vote.28.005 | Negative — Downvoting the top post causes it to drop in the ranking
test('TC.Vote.28.005 UC Negative: downvoting the top post causes it to rank lower in popular sort', function () {
    $user = makeVoter();

    // Both posts created this week
    $postA = makePost($user, 'TC.Vote.28.005 Top Post A ' . uniqid(), 10); // ranked #1
    $postB = makePost($user, 'TC.Vote.28.005 Lower Post B ' . uniqid(), 3); // ranked #2

    $this->browse(function (Browser $browser) use ($user, $postA, $postB) {
        // Step 1 — confirm A is above B
        $browser->loginAs($user)
                ->visit('/community?sort=popular')
                ->waitFor('article', 5)
                ->waitForText($postA->title)
                ->waitForText($postB->title)
                ->pause(500);

        $browser->assertSee($postA->title)->assertSee($postB->title);
        $source = $browser->driver->getPageSource();
        expect(strpos($source, $postA->title))->toBeLessThan(strpos($source, $postB->title));

        $voters = User::factory()->count(15)->create();
        foreach ($voters as $v) {
            PostVote::firstOrCreate(
                ['post_id' => $postA->post_id, 'user_id' => $v->id],
                ['value' => -1]
            );
        }
        $postA->recalculateScore();

        $browser->visit('/community?sort=popular')
                ->waitFor('article', 5)
                ->waitForText($postA->title)
                ->waitForText($postB->title)
                ->pause(500);

        $browser->assertSee($postB->title)->assertSee($postA->title);
        $source = $browser->driver->getPageSource();
        expect(strpos($source, $postB->title))->toBeLessThan(strpos($source, $postA->title));
    });
});
