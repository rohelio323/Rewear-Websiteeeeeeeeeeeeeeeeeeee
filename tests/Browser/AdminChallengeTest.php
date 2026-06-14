<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Challenge;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminChallengeManagementTest extends DuskTestCase
{
    use DatabaseTruncation;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * TC.Challenge.22.001 | Admin can view the challenge management dashboard
     */
    public function testAdminCanViewTheChallengeManagementDashboard()
    {
        $admin = $this->makeAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->assertSee('Challenge Management');
        });
    }

    /**
     * TC.Challenge.22.002 | Admin can successfully create a new active challenge using the modal form
     */
    public function testAdminCanCreateANewActiveChallenge()
    {
        $admin = $this->makeAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->press('Add Challenge')
                    ->pause(500)
                    ->type('title', 'Upcycled Denim Week')
                    ->type('hashtag', 'rewear30days')
                    ->type('description', 'Upcycle your old denim into something new.')
                    ->type('reward_points', '50')
                    ->type('start_date', '01-01-2027')
                    ->type('end_date', '31-01-2027')
                    ->select('status', 'Active')
                    ->press('Launch Challenge')
                    ->pause(1000)
                    ->assertSee('Upcycled Denim Week');
        });
    }

    /**
     * TC.Challenge.22.003 | Admin encounters validation errors when trying to create a challenge without a required title
     */
    public function testAdminEncountersValidationErrorsWithoutTitle()
    {
        $admin = $this->makeAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->press('Add Challenge')
                    ->pause(500)
                    ->type('hashtag', 'rewear30days')
                    ->type('description', 'Upcycle your old denim.')
                    ->type('reward_points', '50')
                    ->type('start_date', '01-01-2027')
                    ->type('end_date', '31-01-2027')
                    ->select('status', 'Active')
                    ->press('Launch Challenge')
                    ->pause(500)
                    ->assertPathIs('/admin/challenges');
        });
    }

    /**
     * TC.Challenge.25.001 | Admin can open the edit modal, modify the challenge details, and save them
     */
    public function testAdminCanEditAChallenge()
    {
        $admin = $this->makeAdmin();
        $challenge = Challenge::create([
            'title' => 'Original Title',
            'hashtag' => 'orig',
            'description' => 'Original description',
            'reward_points' => 10,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->assertSee('Original Title')
                    ->click('button[title="Edit Challenge"]')
                    ->pause(500)
                    ->clear('#edit_title')
                    ->type('#edit_title', 'Updated Title')
                    ->press('Save Changes')
                    ->pause(1000)
                    ->assertSee('Updated Title')
                    ->assertDontSee('Original Title');
        });
    }

    /**
     * TC.Challenge.25.002 | Admin can click delete on a challenge, accept the confirmation modal, and verify the challenge is removed
     */
    public function testAdminCanDeleteAChallenge()
    {
        $admin = $this->makeAdmin();
        $challenge = Challenge::create([
            'title' => 'To Be Deleted',
            'hashtag' => 'delete_me',
            'description' => 'Will be deleted',
            'reward_points' => 0,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'is_active' => false,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->assertSee('To Be Deleted')
                    ->click('button[title="Delete Challenge"]')
                    ->pause(500)
                    ->press('Yes, delete it')
                    ->pause(1000)
                    ->assertDontSee('To Be Deleted');
        });
    }
}
