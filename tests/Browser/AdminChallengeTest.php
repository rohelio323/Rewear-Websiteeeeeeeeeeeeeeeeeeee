<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Challenge;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminChallengeTest extends DuskTestCase
{
    use DatabaseTruncation;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * Case ID: TC.Challenge.22.001
     * Case Type: Positive
     * Description: Admin can view the challenge management dashboard
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
     * Case ID: TC.Challenge.22.002
     * Case Type: Positive
     * Description: Admin can successfully create a new active challenge using the modal form
     */
    public function testAdminCanCreateANewActiveChallenge()
    {
        $admin = $this->makeAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->press('Add Challenge')
                    ->pause(1000)
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
     * Case ID: TC.Challenge.22.003
     * Case Type: Negative
     * Description: Admin encounters validation errors when trying to create a challenge without a required title
     */
    public function testAdminEncountersValidationErrorsWithoutTitle()
    {
        $admin = $this->makeAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->press('Add Challenge')
                    ->pause(1000)
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
     * Case ID: TC.Challenge.25.001
     * Case Type: Positive
     * Description: Admin can open the edit modal, modify the challenge details, and save them
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
                    ->pause(1000)
                    ->clear('#edit_title')
                    ->type('#edit_title', 'Updated Title')
                    ->press('Save Changes')
                    ->pause(1000)
                    ->assertSee('Updated Title')
                    ->assertDontSee('Original Title');
        });
    }

    /**
     * Case ID: TC.Challenge.25.002
     * Case Type: Positive
     * Description: Admin can click delete on a challenge, accept the confirmation modal, and verify the challenge is removed
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

    /**
     * Case ID: TC.Challenge.22.006
     * Case Type: Negative
     * Description: Non-admin user tries to access /admin/challenges
     */
    public function testNonAdminUserTriesToAccessAdminChallenges()
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/admin/challenges')
                    ->assertSee('Access denied.');
        });
    }

    /**
     * Case ID: TC.Challenge.22.007
     * Case Type: Negative
     * Description: Guest (not logged in) tries to access /admin/challenges
     */
    public function testGuestTriesToAccessAdminChallenges()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/admin/challenges')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Case ID: TC.Challenge.22.008
     * Case Type: Negative
     * Description: Admin creates a challenge where the Start Date is after the End Date
     */
    public function testAdminCreatesChallengeWithStartDateAfterEndDate()
    {
        $admin = $this->makeAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->press('Add Challenge')
                    ->pause(500)
                    ->type('title', 'Invalid Dates Challenge')
                    ->type('hashtag', 'invalid_dates')
                    ->type('description', 'Description')
                    ->type('reward_points', '10')
                    ->type('start_date', '10-10-2027')
                    ->type('end_date', '05-10-2027')
                    ->select('status', 'Active')
                    ->press('Launch Challenge')
                    ->pause(500)
                    ->assertSee('Start date cannot be after end date.');
        });
    }

    /**
     * Case ID: TC.Challenge.22.009
     * Case Type: Negative
     * Description: Admin creates a challenge where the Start Date is in the past
     */
    public function testAdminCreatesChallengeWithStartDateInPast()
    {
        $admin = $this->makeAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->press('Add Challenge')
                    ->pause(500)
                    ->type('title', 'Past Start Date Challenge')
                    ->type('hashtag', 'past_start')
                    ->type('description', 'Description')
                    ->type('reward_points', '10')
                    ->type('start_date', now()->subDay()->format('d-m-Y'))
                    ->type('end_date', now()->addDays(5)->format('d-m-Y'))
                    ->select('status', 'Active')
                    ->press('Launch Challenge')
                    ->pause(500)
                    ->assertSee('Start date cannot be in the past.');
        });
    }

    /**
     * Case ID: TC.Challenge.22.010
     * Case Type: Negative
     * Description: Admin attempts to create a challenge with a hashtag that already exists
     */
    public function testAdminAttemptsToCreateChallengeWithDuplicateHashtag()
    {
        $admin = $this->makeAdmin();
        Challenge::create([
            'title' => 'First Challenge',
            'hashtag' => 'existingtag',
            'description' => 'First description',
            'reward_points' => 10,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'is_active' => true,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->press('Add Challenge')
                    ->pause(500)
                    ->type('title', 'Second Challenge')
                    ->type('hashtag', '#existingtag')
                    ->type('description', 'Second description')
                    ->type('reward_points', '20')
                    ->type('start_date', now()->addDays(2)->format('d-m-Y'))
                    ->type('end_date', now()->addDays(6)->format('d-m-Y'))
                    ->select('status', 'Active')
                    ->press('Launch Challenge')
                    ->pause(500)
                    ->assertSee('Hashtag already taken.');
        });
    }

    /**
     * Case ID: TC.Challenge.22.011
     * Case Type: Negative
     * Description: Admin tries to delete a challenge that already has submissions/posts
     */
    public function testAdminCannotDeleteChallengeWithSubmissions()
    {
        $admin = $this->makeAdmin();
        $challenge = Challenge::create([
            'title' => 'Popular Challenge',
            'hashtag' => 'popular',
            'description' => 'Challenge with posts',
            'reward_points' => 15,
            'start_date' => now()->addDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        \App\Models\Post::create([
            'title' => 'My Submission',
            'content' => 'Outfit for popular challenge',
            'users_id' => $user->id,
            'tags' => 'popular, othertag',
            'upvote_count' => 0,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->assertSee('Popular Challenge')
                    ->click('button[title="Delete Challenge"]')
                    ->pause(500)
                    ->press('Yes, delete it')
                    ->pause(1000)
                    ->assertSee('Cannot delete challenge with existing submissions.');
        });
    }

    /**
     * Case ID: TC.Challenge.22.012
     * Case Type: Positive
     * Description: Admin visits the dashboard when zero challenges exist
     */
    public function testAdminVisitsDashboardWhenZeroChallengesExist()
    {
        Challenge::truncate();
        $admin = $this->makeAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/challenges')
                    ->assertSee('No challenges found');
        });
    }
}
