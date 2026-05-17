<?php

use App\Models\User;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Artisan;

uses(DatabaseTruncation::class)->beforeAll(function () {
    Artisan::call('view:clear');
});

function rowXPath(int|string $userId): string
{
    return "//tr[@data-user-id='{$userId}']";
}

test('TC.Admin.08.001 non-admin cannot access dashboard', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/admin/dashboard')
                ->assertSee('403');
    });
});

test('TC.Admin.08.002 admin can access the dashboard and see the page header', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/dashboard')
            ->assertPathIs('/admin/dashboard')
            ->assertSee('System Overview');
    });
});

test('TC.Admin.08.003 admin dashboard shows KPI cards, sustainability and marketplace sections', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/dashboard')
            ->pause(800)
            ->assertSee('TOTAL USERS')
            ->assertSee('TOTAL ORDERS')
            ->assertSee('TOTAL LISTINGS')
            ->assertSee('CO2 SAVED GLOBALLY')
            ->assertSee('Tons')
            ->assertSee('Marketplace Activity')
            ->assertSee('New listings over time');
    });
});

test('TC.Admin.08.004 total users KPI matches actual registered user count and updates after registration', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();

    $initialCount = User::count();

    $this->browse(function (Browser $browser) use ($admin, $initialCount) {
        $browser->loginAs($admin)
            ->visit('/admin/dashboard')
            ->assertSee(number_format($initialCount));

        User::factory()->create();

        $browser->refresh()
            ->assertSee(number_format($initialCount + 1));
    });
});

test('TC.Admin.08.005 admin dashboard shows total orders, total listings KPIs and recent activity section', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/dashboard')
            ->pause(800)
            ->assertSee('TOTAL USERS')
            ->assertSee('TOTAL LISTINGS')
            ->assertSee('Recent Activity')
            ->assertSee('No recent activity found.');
    });
});

test('TC.Admin.09.001 admin can navigate to the user moderation page', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->assertPathIs('/admin/users')
            ->assertSee('Active Community Members')
            ->assertSee('Manage platform users, verify sellers, and oversee community health.');
    });
});

test('TC.Admin.09.002 user moderation page shows stat cards and the user table columns', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->pause(800)
            ->assertSee('TOTAL USERS')
            ->assertSee('VERIFIED SELLERS')
            ->assertSee('PENDING SELLERS')
            ->assertSee('FLAGGED / SUSPENDED')
            ->assertSee('USER')
            ->assertSee('ROLE & STATUS')
            ->assertSee('SELLER STATUS')
            ->assertSee('IMPACT')
            ->assertSee('JOINED')
            ->assertSee('ACTIONS');
    });
});

test('TC.Admin.09.003 admin can filter users via the search bar and clear the filter', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();
    $target = User::factory()->create([
        'name'  => 'Jane Doe TestUser',
        'email' => 'jane.doe.test@example.com',
    ]);
    $other = User::factory()->create([
        'name'  => 'Bob Smith Other',
        'email' => 'bob.smith.other@example.com',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $target, $other) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->type('search', 'Jane Doe TestUser')
            ->keys('input[name=search]', '{enter}')
            ->waitForLocation('/admin/users')
            ->assertSee($target->name)
            ->assertDontSee($other->name)
            ->click('a[title="Clear Search"]')
            ->waitForLocation('/admin/users')
            ->assertSee($target->name)
            ->assertSee($other->name);
    });
});

test('TC.Admin.09.004 admin can suspend a user via the confirmation dialog', function () {
    $this->seed();
    $admin  = User::where('role', 'admin')->firstOrFail();
    $victim = User::factory()->create([
        'name' => 'Suspend Target',
        'role' => 'user',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $victim) {
        $rowXPath = rowXPath($victim->id);

        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->clickAtXPath("{$rowXPath}//button[@title='Suspend User']")
            ->acceptDialog()
            ->waitForLocation('/admin/users')
            ->assertSee('Suspended');

        expect($victim->fresh()->trashed())->toBeTrue();
    });
});

test('TC.Admin.09.005 cancelling the suspend dialog leaves the user active', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();
    $user  = User::factory()->create([
        'name' => 'Stay Active User',
        'role' => 'user',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $user) {
        $rowXPath = rowXPath($user->id);

        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->clickAtXPath("{$rowXPath}//button[@title='Suspend User']")
            ->dismissDialog()
            ->pause(500);

        expect($user->fresh()->trashed())->toBeFalse();

        $stillThere = $browser->driver->findElements(
            \Facebook\WebDriver\WebDriverBy::xpath("{$rowXPath}//button[@title='Suspend User']")
        );
        expect(count($stillThere))->toBeGreaterThan(0);
    });
});

test('TC.Admin.09.006 admin row does not expose a suspend button for the logged-in admin', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();

    $this->browse(function (Browser $browser) use ($admin) {
        $rowXPath = rowXPath($admin->id);

        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->assertSee('Admin');

        $suspendBtns = $browser->driver->findElements(
            \Facebook\WebDriver\WebDriverBy::xpath("{$rowXPath}//button[@title='Suspend User']")
        );
        expect(count($suspendBtns))->toBe(0);

        $viewLinks = $browser->driver->findElements(
            \Facebook\WebDriver\WebDriverBy::xpath("{$rowXPath}//a[@title='View Profile']")
        );
        expect(count($viewLinks))->toBeGreaterThan(0);
    });
});

test('TC.Admin.09.007 admin can open a user detail page and return back to the list', function () {
    $this->seed();
    $admin  = User::where('role', 'admin')->firstOrFail();
    $target = User::factory()->create([
        'name' => 'Detail Page User',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $target) {
        $rowXPath = rowXPath($target->id);

        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->pause(500)
            ->clickAtXPath("{$rowXPath}//a[@title='View Profile']")
            ->pause(800)
            ->assertPathIs("/admin/users/{$target->id}")
            ->assertSee($target->name)
            ->assertSee($target->email)
            ->assertSee('ACTIVE LISTINGS')
            ->assertSee('TOTAL PURCHASES')
            ->assertSee('CO₂ SAVED')
            ->clickLink('Back to Users')
            ->assertPathIs('/admin/users');
    });
});

test('TC.Admin.10.001 users with pending seller requests show pending badge with approve and reject buttons', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();
    $applicant = User::factory()->create([
        'name' => 'Seller Applicant',
        'role' => 'user',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $applicant) {
        $browser->loginAs($applicant)
            ->visit('/profile')
            ->press('Become a Seller')
            ->waitForText('Application Pending', 10)
            ->assertSee('Application Pending');

        $rowXPath = rowXPath($applicant->id);

        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->assertSee('PENDING REQUEST');

        $approveBtns = $browser->driver->findElements(
            \Facebook\WebDriver\WebDriverBy::xpath("{$rowXPath}//button[normalize-space(text())='Approve']")
        );
        expect(count($approveBtns))->toBeGreaterThan(0);

        $rejectBtns = $browser->driver->findElements(
            \Facebook\WebDriver\WebDriverBy::xpath("{$rowXPath}//button[normalize-space(text())='Reject']")
        );
        expect(count($rejectBtns))->toBeGreaterThan(0);

        expect($applicant->fresh()->seller_request_status)->toBe('pending');
    });
});

test('TC.Admin.10.002 admin can approve a pending seller request and the user becomes a verified seller', function () {
    $this->seed();
    $admin   = User::where('role', 'admin')->firstOrFail();
    $pending = User::factory()->create([
        'name'                  => 'Approve Me Seller',
        'role'                  => 'user',
        'seller_request_status' => 'pending',
    ]);

    $this->browse(function (Browser $browser) use ($admin, $pending) {
        $rowXPath = rowXPath($pending->id);

        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->clickAtXPath("{$rowXPath}//button[normalize-space(text())='Approve']")
            ->waitForLocation('/admin/users')
            ->assertSee('Verified Seller');

        $fresh = $pending->fresh();
        expect($fresh->seller_request_status)->toBe('approved');
        expect((bool) $fresh->is_verified_seller)->toBeTrue();
    });
});

test('TC.Admin.10.003 reject seller modal opens with note field and cancel button closes it without changes', function () {
    $this->seed();
    $admin   = User::where('role', 'admin')->firstOrFail();
    $pending = User::factory()->create([
        'name'                  => 'Reject Modal User',
        'role'                  => 'user',
        'seller_request_status' => 'pending',
    ]);

    $modal    = "#reject-modal-{$pending->id}";
    $rowXPath = rowXPath($pending->id);

    $this->browse(function (Browser $browser) use ($admin, $pending, $modal, $rowXPath) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->clickAtXPath("{$rowXPath}//button[normalize-space(text())='Reject']")
            ->waitFor($modal)
            ->assertSeeIn($modal, 'Reject Seller Request')
            ->assertSeeIn($modal, $pending->name)
            ->assertVisible($modal . ' textarea[name="note"]')
            ->assertSeeIn($modal, 'Cancel')
            ->assertSeeIn($modal, 'Confirm Reject')
            ->click($modal . ' button[type="button"]')
            ->pause(500);

        $hidden = $browser->driver->findElements(
            \Facebook\WebDriver\WebDriverBy::cssSelector("{$modal}.hidden")
        );
        expect(count($hidden))->toBeGreaterThan(0);
        expect($pending->fresh()->seller_request_status)->toBe('pending');
    });
});

test('TC.Admin.10.004 rejecting a seller request with an empty note does not submit', function () {
    $this->seed();
    $admin   = User::where('role', 'admin')->firstOrFail();
    $pending = User::factory()->create([
        'name'                  => 'Empty Note Reject',
        'role'                  => 'user',
        'seller_request_status' => 'pending',
    ]);

    $modal    = "#reject-modal-{$pending->id}";
    $rowXPath = rowXPath($pending->id);

    $this->browse(function (Browser $browser) use ($admin, $pending, $modal, $rowXPath) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->clickAtXPath("{$rowXPath}//button[normalize-space(text())='Reject']")
            ->waitFor($modal)
            ->click($modal . ' button[type="submit"]')
            ->pause(500)
            ->assertVisible($modal)
            ->assertAttribute($modal . ' textarea[name="note"]', 'required', 'true');

        expect($pending->fresh()->seller_request_status)->toBe('pending');
    });
});

test('TC.Admin.10.005 admin can reject a seller request with a valid reason note', function () {
    $this->seed();
    $admin   = User::where('role', 'admin')->firstOrFail();
    $pending = User::factory()->create([
        'name'                  => 'Reject With Reason',
        'role'                  => 'user',
        'seller_request_status' => 'pending',
    ]);

    $modal    = "#reject-modal-{$pending->id}";
    $rowXPath = rowXPath($pending->id);

    $this->browse(function (Browser $browser) use ($admin, $pending, $modal, $rowXPath) {
        $browser->loginAs($admin)
            ->visit('/admin/users')
            ->clickAtXPath("{$rowXPath}//button[normalize-space(text())='Reject']")
            ->waitFor($modal)
            ->type($modal . ' textarea[name="note"]', 'ID document is unclear')
            ->click($modal . ' button[type="submit"]')
            ->waitForLocation('/admin/users')
            ->assertSee('Rejected');

        $fresh = $pending->fresh();
        expect($fresh->seller_request_status)->toBe('rejected');
        expect((bool) $fresh->is_verified_seller)->toBeFalse();
    });
});

test('TC.Admin.10.006 a user whose seller request was rejected cannot access the create listing flow', function () {
    $rejected = User::factory()->create([
        'role'                  => 'user',
        'seller_request_status' => 'rejected',
        'is_verified_seller'    => false,
    ]);

    $this->browse(function (Browser $browser) use ($rejected) {
        $browser->loginAs($rejected)
            ->visit('/items/create')
            ->assertDontSee('Create Listing Form');

        $current = $browser->driver->getCurrentURL();
        expect(str_contains($current, '/items/create'))->toBeFalse();
    });
});

test('TC.Admin.10.007 approved and rejected seller rows no longer show approve or reject buttons', function () {
    $this->seed();
    $admin = User::where('role', 'admin')->firstOrFail();

    $approved = User::factory()->create([
        'name'                  => 'Already Approved',
        'role'                  => 'user',
        'seller_request_status' => 'approved',
        'is_verified_seller'    => true,
    ]);

    $rejected = User::factory()->create([
        'name'                  => 'Already Rejected',
        'role'                  => 'user',
        'seller_request_status' => 'rejected',
        'is_verified_seller'    => false,
    ]);

    $this->browse(function (Browser $browser) use ($admin, $approved, $rejected) {
        $approvedRow = rowXPath($approved->id);
        $rejectedRow = rowXPath($rejected->id);

        $browser->loginAs($admin)->visit('/admin/users');

        $approvedActions = $browser->driver->findElements(
            \Facebook\WebDriver\WebDriverBy::xpath(
                "{$approvedRow}//button[normalize-space(text())='Approve' or normalize-space(text())='Reject']"
            )
        );
        expect(count($approvedActions))->toBe(0);

        $rejectedActions = $browser->driver->findElements(
            \Facebook\WebDriver\WebDriverBy::xpath(
                "{$rejectedRow}//button[normalize-space(text())='Approve' or normalize-space(text())='Reject']"
            )
        );
        expect(count($rejectedActions))->toBe(0);

        $browser->assertSee('Verified Seller');
    });
});