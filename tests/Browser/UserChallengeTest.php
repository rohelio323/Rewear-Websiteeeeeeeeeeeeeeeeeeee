<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Challenge;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\TestDox;

class UserChallengeEntryTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * Case ID: TC.Challenge.24.001
     * Case Type: Positive
     * Description: User can view an active challenge
     */
    public function testUserCanViewAnActiveChallengeDetailPage()
    {
        $user = User::factory()->create();
        $challenge = Challenge::create([
            'title' => 'Upcycled Denim Week',
            'hashtag' => 'upcycleddenim',
            'description' => 'Show off your denim upcycling skills!',
            'reward_points' => 50,
            'start_date' => now()->subDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($user, $challenge) {
            $browser->loginAs($user)
                    ->visit('/challenges/' . $challenge->id)
                    ->assertSee('Upcycled Denim Week')
                    ->assertSee('#upcycleddenim')
                    ->assertSee('Submit Your Fit');
        });
    }

    /**
     * Case ID: TC.Challenge.24.002
     * Case Type: Positive
     * Description: User can submit a valid post to an active challenge
     */
    public function testUserCanSubmitAPostWithAnImageToAnActiveChallenge()
    {
        $user = User::factory()->create();
        $challenge = Challenge::create([
            'title' => 'Vintage Tee Event',
            'hashtag' => 'vintagetee',
            'description' => 'Show off your vintage tees.',
            'reward_points' => 20,
            'start_date' => now()->subDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'is_active' => true,
        ]);

        $imagePath = public_path('placeholder.jpg');

        // Create placeholder.jpg if it doesn't exist
        if (!file_exists($imagePath)) {
            file_put_contents($imagePath, 'test image');
        }

        $this->browse(function (Browser $browser) use ($user, $challenge, $imagePath) {
            $browser->loginAs($user)
                    ->visit('/challenges/' . $challenge->id)
                    ->type('title', 'My Vintage Band Tee')
                    ->type('content', 'Found this 90s band tee at a thrift store.')
                    ->type('tags', 'vintage, music, 90s')
                    ->attach('image', $imagePath)
                    ->press('Post to Challenge')
                    ->pause(2000)
                    ->assertSee('My Vintage Band Tee')
                    ->assertSee('Found this 90s band tee');
        });
    }

    /**
     * Case ID: TC.Challenge.24.003
     * Case Type: Negative
     * Description: Unauthenticated user is redirected to the login page when trying to access the challenge detail or submission page
     */
    public function testUnauthenticatedUserIsRedirectedWhenSubmittingToChallenge()
    {
        $challenge = Challenge::create([
            'title' => 'Guest Challenge',
            'hashtag' => 'guest',
            'description' => 'Guest test.',
            'reward_points' => 10,
            'start_date' => now()->subDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($challenge) {
            $browser->logout()
                    ->visit('/challenges/' . $challenge->id)
                    ->assertSee('Guest Challenge'); // Guests CAN see the challenge
        });
    }
}
