<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PersonalStatsTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * TC.Stats.23.001 | User can view their calculated Total Score (Points) and Leaderboard Rank
     */
    public function testUserCanViewTheirTotalScoreAndRank()
    {
        // Create a user with a specific score
        $user1 = User::factory()->create(['name' => 'Top Scorer']);
        Post::create([
            'title' => 'Top Post',
            'content' => 'Great outfit.',
            'upvote_count' => 100,
            'users_id' => $user1->id,
            'image_path' => 'dummy.jpg'
        ]);

        $user2 = User::factory()->create(['name' => 'Second Scorer']);
        Post::create([
            'title' => 'Second Post',
            'content' => 'Cool outfit.',
            'upvote_count' => 50,
            'users_id' => $user2->id,
            'image_path' => 'dummy2.jpg'
        ]);

        $this->browse(function (Browser $browser) use ($user2) {
            $browser->loginAs($user2)
                    ->visit('/profile') // The stats are displayed on the profile edit page
                    // We expect to see a score of 50 and a rank of #2
                    ->assertSee('50')
                    ->assertSee('#2');
        });
    }

    /**
     * TC.Stats.23.002 | User can view their documented "Challenge History" on the profile page
     */
    public function testUserCanViewTheirChallengeHistory()
    {
        $user = User::factory()->create();
        
        // Create a challenge post (with tags)
        Post::create([
            'title' => 'My Challenge Outfit',
            'content' => 'Participating in the denim challenge.',
            'tags' => 'denimweek, upcycle',
            'upvote_count' => 10,
            'users_id' => $user->id,
            'image_path' => 'dummy3.jpg'
        ]);

        // Create a regular post (no tags)
        Post::create([
            'title' => 'Regular Outfit',
            'content' => 'Just a regular post.',
            'tags' => null,
            'upvote_count' => 5,
            'users_id' => $user->id,
            'image_path' => 'dummy4.jpg'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertSee('Challenge History')
                    ->assertSee('My Challenge Outfit')
                    ->assertSee('DENIMWEEK')
                    ->assertDontSee('Regular Outfit');
        });
    }

    /**
     * TC.Stats.23.003 | A new user with no posts sees the "No stories yet" empty state
     */
    public function testNewUserSeesEmptyChallengeHistoryState()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertSee('No stories yet')
                    ->assertSee('Head to the Living Archive to join a challenge!');
        });
    }
}
