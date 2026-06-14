<?php

use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Report;
use App\Models\Post;

function ensureReport(): Report
{
    $existing = Report::where('status', 'pending')->first();
    if ($existing) {
        return $existing;
    }

    $reporter = User::where('role', '!=', 'admin')->first();

    $post = Post::where('users_id', $reporter->id)->first();
    if (!$post) {
        $post = Post::create([
            'users_id' => $reporter->id,
            'title'    => 'Dusk Test Post',
            'content'  => 'Auto-created by dusk test',
        ]);
    }

    return Report::create([
        'reportable_type' => 'App\\Models\\Post',
        'reportable_id'   => $post->post_id,
        'reporter_id'     => $reporter->id,
        'reason'          => 'Test report for dusk',
        'status'          => 'pending',
    ]);
}


test('TC.Mod.35.001 an admin can view the moderation queue page', function () {
    $admin = User::where('role', 'admin')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/moderation')
                ->waitForText('Moderation Queue', 10)
                ->assertSee('Moderation Queue')
                ->assertSee('pending')
                ->screenshot('tc-mod-35-001-dashboard');
    });
});

test('TC.Mod.35.002 the dashboard displays reported content cards with action buttons', function () {
    $admin = User::where('role', 'admin')->first();
    ensureReport();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/moderation')
                ->waitForText('Hide', 10)
                ->assertSee('Hide')
                ->assertSee('Warn User')
                ->assertSee('Delete')
                ->assertSee('Dismiss')
                ->screenshot('tc-mod-35-002-report-cards');
    });
});


test('TC.Mod.35.003 a regular user cannot access the moderation dashboard', function () {
    $user = User::where('role', '!=', 'admin')->first();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/admin/moderation')
                ->pause(1500)
                ->assertDontSee('Moderation Queue')  
                ->screenshot('tc-mod-35-003-unauthorized');
    });
});

test('TC.Mod.35.004 the dashboard shows all clear message when there are no reports', function () {
    $admin = User::where('role', 'admin')->first();

    Report::query()->delete();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/moderation')
                ->waitForText('All clear!', 10)
                ->assertSee('All clear!')
                ->assertSee('No pending reports to review.')
                ->screenshot('tc-mod-35-004-empty-queue');
    });
});

test('TC.Mod.36.001 an admin can hide reported content', function () {
    $admin = User::where('role', 'admin')->first();
    ensureReport();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/moderation')
                ->waitForText('Hide', 10)
                ->screenshot('tc-mod-36-001-before-hide');

        $browser->with('.space-y-4 > div:first-child', function ($card) {
            $card->press('Hide');
        })
        ->waitForText('Content hidden', 10)
        ->assertSee('Content hidden')
        ->screenshot('tc-mod-36-001-after-hide');
    });
});

test('TC.Mod.36.002 an admin can permanently delete reported content', function () {
    $admin = User::where('role', 'admin')->first();
    ensureReport();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/moderation')
                ->waitForText('Delete', 10)
                ->screenshot('tc-mod-36-002-before-delete');

        $browser->with('.space-y-4 > div:first-child', function ($card) {
            $card->press('Delete');
        })
        ->acceptDialog()
        ->waitForText('Content permanently deleted', 10)
        ->assertSee('Content permanently deleted')
        ->screenshot('tc-mod-36-002-after-delete');
    });
});

test('TC.Mod.36.003 an admin can dismiss a report with no action', function () {
    $admin = User::where('role', 'admin')->first();
    ensureReport();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/moderation')
                ->waitForText('Dismiss', 10);

        $browser->with('.space-y-4 > div:first-child', function ($card) {
            $card->press('Dismiss');
        })
        ->waitForText('Report dismissed', 10)
        ->assertSee('Report dismissed')
        ->screenshot('tc-mod-36-003-dismiss');
    });
});


test('TC.Mod.36.004 a regular user cannot access a moderation action route directly', function () {
    $user   = User::where('role', '!=', 'admin')->first();
    $report = ensureReport();

    $this->browse(function (Browser $browser) use ($user, $report) {
        $browser->loginAs($user)
                ->visit('/admin/moderation/' . $report->id . '/hide')
                ->pause(1500)
                ->assertDontSee('Content hidden')   
                ->screenshot('tc-mod-36-004-unauthorized-action');
    });
});


test('TC.Mod.36.005 visiting a non-existent report id does not crash the app', function () {
    $admin = User::where('role', 'admin')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/moderation/999999')
                ->pause(1500)
                ->assertDontSee('Content hidden')
                ->assertDontSee('Warning issued')
                ->screenshot('tc-mod-36-005-nonexistent-report');
    });
});


test('TC.Mod.37.001 an admin can warn a user from a reported content card', function () {
    $admin = User::where('role', 'admin')->first();
    ensureReport();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/moderation')
                ->waitForText('Warn User', 10)
                ->assertSee('Warn User');

        $browser->with('.space-y-4 > div:first-child', function ($card) {
            $card->press('Warn User');
        })
        ->waitForText('Warning issued', 10)
        ->assertSee('Warning issued to the content owner')
        ->screenshot('tc-mod-37-001-warn-issued');
    });
});

test('TC.Mod.37.002 a warned user sees the warning banner on their account settings page', function () {
    $warnedUser = User::where('role', '!=', 'admin')->first();
    $warnedUser->update(['warning_count' => 1]);

    $this->browse(function (Browser $browser) use ($warnedUser) {
        $browser->loginAs($warnedUser)
                ->visit('/profile')
                ->waitForText('warning', 10)
                ->assertSee('warning')
                ->screenshot('tc-mod-37-002-warning-banner');
    });
});

test('TC.Mod.37.003 a clean user does not see a warning banner on their account settings page', function () {
    $cleanUser = User::where('role', '!=', 'admin')->first();
    $cleanUser->update(['warning_count' => 0]);

    $this->browse(function (Browser $browser) use ($cleanUser) {
        $browser->loginAs($cleanUser)
                ->visit('/profile')
                ->pause(1500)
                ->assertDontSee('Your account has')
                ->screenshot('tc-mod-37-003-no-warning');
    });
});

test('TC.Mod.37.004 warning count increases each time admin warns the same user', function () {
    $admin = User::where('role', 'admin')->first();

    $targetUser = User::where('role', '!=', 'admin')->first();
    $targetUser->update(['warning_count' => 0]);

    $reporter = $targetUser;
    $post1 = Post::create([
        'users_id' => $targetUser->id,
        'title'    => 'Warn Test Post 1',
        'content'  => 'test',
    ]);
    $post2 = Post::create([
        'users_id' => $targetUser->id,
        'title'    => 'Warn Test Post 2',
        'content'  => 'test',
    ]);

    Report::create([
        'reportable_type' => 'App\\Models\\Post',
        'reportable_id'   => $post1->post_id,
        'reporter_id'     => $admin->id,
        'reason'          => 'warn test 1',
        'status'          => 'pending',
    ]);
    Report::create([
        'reportable_type' => 'App\\Models\\Post',
        'reportable_id'   => $post2->post_id,
        'reporter_id'     => $admin->id,
        'reason'          => 'warn test 2',
        'status'          => 'pending',
    ]);

    $this->browse(function (Browser $browser) use ($admin) {

        $browser->loginAs($admin)
                ->visit('/admin/moderation')
                ->waitForText('Warn User', 10);

        $browser->with('.space-y-4 > div:first-child', function ($card) {
            $card->press('Warn User');
        })
        ->waitForText('Warning issued', 10);

        $browser->visit('/admin/moderation')
                ->waitForText('Warn User', 10);

        $browser->with('.space-y-4 > div:first-child', function ($card) {
            $card->press('Warn User');
        })
        ->waitForText('Warning issued', 10)
        ->screenshot('tc-mod-37-004-double-warn');
    });


    $targetUser->refresh();
    expect($targetUser->warning_count)->toBeGreaterThanOrEqual(2);
});


test('TC.Mod.37.005 a guest user is redirected away from the moderation page', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()
                ->visit('/admin/moderation')
                ->pause(1500)
                ->assertDontSee('Moderation Queue')
                ->screenshot('tc-mod-37-005-guest-blocked');
    });
});