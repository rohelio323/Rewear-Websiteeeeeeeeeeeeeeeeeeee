<?php

use App\Models\CarbonVoucher;
use App\Models\Item;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Laravel\Dusk\Browser;

// ==========================================
// PBI-29: MARKETPLACE ITEM REPORTING
// ==========================================

test('TC.Report.29.001 - Positive (UC): Logged-in buyer sees report flag button on item page', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        $browser->loginAs($buyer)
            ->visit('/item/detail/' . $item->id)
            ->assertPresent('button[title="Report this listing"]');
    });
});

test('TC.Report.29.002 - Negative (EP): Guest user does not see report button on item page', function () {
    $this->browse(function (Browser $browser) {
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        $browser->logout()
            ->visit('/item/detail/' . $item->id)
            ->assertMissing('button[title="Report this listing"]');
    });
});

test('TC.Report.29.003 - Positive (UC): Clicking flag icon opens the item report modal', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        $browser->loginAs($buyer)
            ->visit('/item/detail/' . $item->id)
            ->click('button[title="Report this listing"]')
            ->waitFor('#itemReportModal', 3)
            ->assertVisible('#itemReportModal');
    });
});

test('TC.Report.29.004 - Positive (UC): User successfully submits an item report with a valid reason', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        // Remove any prior pending report to avoid duplicate error
        Report::where('reportable_type', Item::class)
            ->where('reportable_id', $item->id)
            ->where('reporter_id', $buyer->id)
            ->where('status', 'pending')
            ->delete();

        $browser->loginAs($buyer)
            ->visit('/item/detail/' . $item->id)
            ->click('button[title="Report this listing"]')
            ->waitFor('#itemReportModal', 3)
            ->type('textarea[name="reason"]', 'This item looks like a scam.')
            ->press('Submit Report')
            ->pause(1000)
            ->assertSee('Report submitted');
    });
});

test('TC.Report.29.005 - Negative (EP): System rejects item report submission with empty reason', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        $browser->loginAs($buyer)
            ->visit('/item/detail/' . $item->id)
            ->click('button[title="Report this listing"]')
            ->waitFor('#itemReportModal', 3)
            ->press('Submit Report')
            // HTML5 required attribute blocks submission; user stays on item page
            ->assertPathIs('/item/detail/' . $item->id);
    });
});

test('TC.Report.29.006 - Positive (BVA): Item report accepted with exactly 1000 character reason', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        Report::where('reportable_type', Item::class)
            ->where('reportable_id', $item->id)
            ->where('reporter_id', $buyer->id)
            ->where('status', 'pending')
            ->delete();

        $browser->loginAs($buyer)
            ->visit('/item/detail/' . $item->id)
            ->click('button[title="Report this listing"]')
            ->waitFor('#itemReportModal', 3)
            ->type('textarea[name="reason"]', str_repeat('a', 1000))
            ->press('Submit Report')
            ->pause(1000)
            ->assertSee('Report submitted');
    });
});

test('TC.Report.29.007 - Negative (BVA): Item report rejected with 1001 character reason', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        $browser->loginAs($buyer)
            ->visit('/item/detail/' . $item->id)
            ->click('button[title="Report this listing"]')
            ->waitFor('#itemReportModal', 3)
            ->type('textarea[name="reason"]', str_repeat('a', 1001))
            ->press('Submit Report')
            ->assertDontSee('Report submitted');
    });
});

test('TC.Report.29.008 - Negative (EP): User cannot submit duplicate pending report on same item', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        // Ensure a prior pending report already exists
        Report::firstOrCreate(
            [
                'reportable_type' => Item::class,
                'reportable_id'   => $item->id,
                'reporter_id'     => $buyer->id,
                'status'          => 'pending',
            ],
            ['reason' => 'First report already submitted.']
        );

        $browser->loginAs($buyer)
            ->visit('/item/detail/' . $item->id)
            ->click('button[title="Report this listing"]')
            ->waitFor('#itemReportModal', 3)
            ->type('textarea[name="reason"]', 'Trying to report again.')
            ->press('Submit Report')
            ->pause(1000)
            ->assertSee('You have already reported this content.');
    });
});

test('TC.Report.29.009 - Positive (UC): Reported item appears in admin moderation panel', function () {
    $this->browse(function (Browser $browser) {
        $admin  = User::where('email', 'admin@rewear.com')->firstOrFail();
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        Report::firstOrCreate(
            [
                'reportable_type' => Item::class,
                'reportable_id'   => $item->id,
                'reporter_id'     => $buyer->id,
                'status'          => 'pending',
            ],
            ['reason' => 'Suspicious item for moderation test.']
        );

        $browser->loginAs($admin)
            ->visit('/admin/moderation')
            ->assertSee('Moderation Queue')
            ->assertSee('Buyer Test');
    });
});

test('TC.Report.29.010 - Positive (UC): Admin can dismiss a pending item report', function () {
    $this->browse(function (Browser $browser) {
        $admin  = User::where('email', 'admin@rewear.com')->firstOrFail();
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $item   = Item::where('users_id', $seller->id)->firstOrFail();

        // Clean state: only one pending report
        Report::where('status', 'pending')->delete();

        $uniqueReason = 'Dismiss test item ' . time();
        Report::create([
            'reportable_type' => Item::class,
            'reportable_id'   => $item->id,
            'reporter_id'     => $buyer->id,
            'reason'          => $uniqueReason,
            'status'          => 'pending',
        ]);

        $browser->loginAs($admin)
            ->visit('/admin/moderation')
            ->assertSee($uniqueReason)
            ->press('Dismiss')
            ->pause(1000)
            ->assertDontSee($uniqueReason);
    });
});

// ==========================================
// PBI-30: COMMUNITY POST REPORTING
// ==========================================

test('TC.Report.30.001 - Positive (UC): Logged-in user sees report option in community post kebab menu', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post   = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 1',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId = $post->post_id;

        $browser->loginAs($buyer)
            ->visit('/community')
            ->click("button[onclick=\"toggleDropdown({$postId})\"]")
            ->waitFor('#dropdown-' . $postId, 2)
            ->assertSeeIn('#dropdown-' . $postId, 'Report');
    });
});

test('TC.Report.30.002 - Negative (EP): Guest user does not see the kebab menu on community posts', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()
            ->visit('/community')
            ->assertMissing('.kebab-button');
    });
});

test('TC.Report.30.003 - Positive (UC): Clicking Report in kebab menu opens the post report modal', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post   = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 2',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId = $post->post_id;

        $browser->loginAs($buyer)
            ->visit('/community')
            ->click("button[onclick=\"toggleDropdown({$postId})\"]")
            ->waitFor('#dropdown-' . $postId, 2)
            ->click('#dropdown-' . $postId . ' button[onclick^="openReportModal"]')
            ->waitFor('#reportPostModal', 3)
            ->pause(400)
            ->assertVisible('#reportPostModal');
    });
});

test('TC.Report.30.004 - Positive (UC): User successfully submits a post report with a valid reason', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post   = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 3',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId = $post->post_id;

        // Remove any prior pending report
        Report::where('reportable_type', Post::class)
            ->where('reportable_id', $postId)
            ->where('reporter_id', $buyer->id)
            ->where('status', 'pending')
            ->delete();

        $browser->loginAs($buyer)
            ->visit('/community')
            ->click("button[onclick=\"toggleDropdown({$postId})\"]")
            ->waitFor('#dropdown-' . $postId, 2)
            ->click('#dropdown-' . $postId . ' button[onclick^="openReportModal"]')
            ->waitFor('#reportPostModal', 3)
            ->pause(400)
            ->type('textarea[name="reason"]', 'This post promotes non-sustainable fast fashion.')
            ->press('Submit Report')
            ->pause(1000)
            ->assertSee('Report submitted');
    });
});

test('TC.Report.30.005 - Negative (EP): System rejects post report submission with empty reason', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post   = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 4',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId = $post->post_id;

        $browser->loginAs($buyer)
            ->visit('/community')
            ->click("button[onclick=\"toggleDropdown({$postId})\"]")
            ->waitFor('#dropdown-' . $postId, 2)
            ->click('#dropdown-' . $postId . ' button[onclick^="openReportModal"]')
            ->waitFor('#reportPostModal', 3)
            ->pause(400)
            ->press('Submit Report')
            // HTML5 required attribute blocks submission; user stays on community page
            ->assertPathIs('/community');
    });
});

test('TC.Report.30.006 - Positive (BVA): Post report accepted with exactly 1000 character reason', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post   = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 5',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId = $post->post_id;

        Report::where('reportable_type', Post::class)
            ->where('reportable_id', $postId)
            ->where('reporter_id', $buyer->id)
            ->where('status', 'pending')
            ->delete();

        $browser->loginAs($buyer)
            ->visit('/community')
            ->click("button[onclick=\"toggleDropdown({$postId})\"]")
            ->waitFor('#dropdown-' . $postId, 2)
            ->click('#dropdown-' . $postId . ' button[onclick^="openReportModal"]')
            ->waitFor('#reportPostModal', 3)
            ->pause(400)
            ->type('textarea[name="reason"]', str_repeat('a', 1000))
            ->press('Submit Report')
            ->pause(1000)
            ->assertSee('Report submitted');
    });
});

test('TC.Report.30.007 - Negative (BVA): Post report rejected with 1001 character reason', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post   = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 6',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId = $post->post_id;

        $browser->loginAs($buyer)
            ->visit('/community')
            ->click("button[onclick=\"toggleDropdown({$postId})\"]")
            ->waitFor('#dropdown-' . $postId, 2)
            ->click('#dropdown-' . $postId . ' button[onclick^="openReportModal"]')
            ->waitFor('#reportPostModal', 3)
            ->pause(400)
            ->type('textarea[name="reason"]', str_repeat('a', 1001))
            ->press('Submit Report')
            ->assertDontSee('Report submitted');
    });
});

test('TC.Report.30.008 - Negative (EP): User cannot submit duplicate pending report on same post', function () {
    $this->browse(function (Browser $browser) {
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post   = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 7',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId = $post->post_id;

        // Ensure a prior pending report exists
        Report::firstOrCreate(
            [
                'reportable_type' => Post::class,
                'reportable_id'   => $postId,
                'reporter_id'     => $buyer->id,
                'status'          => 'pending',
            ],
            ['reason' => 'First post report already submitted.']
        );

        $browser->loginAs($buyer)
            ->visit('/community')
            ->click("button[onclick=\"toggleDropdown({$postId})\"]")
            ->waitFor('#dropdown-' . $postId, 2)
            ->click('#dropdown-' . $postId . ' button[onclick^="openReportModal"]')
            ->waitFor('#reportPostModal', 3)
            ->pause(400)
            ->type('textarea[name="reason"]', 'Trying to report the same post again.')
            ->press('Submit Report')
            ->pause(1000)
            ->assertSee('You have already reported this content.');
    });
});

test('TC.Report.30.009 - Positive (UC): Reported post appears in admin moderation panel', function () {
    $this->browse(function (Browser $browser) {
        $admin  = User::where('email', 'admin@rewear.com')->firstOrFail();
        $buyer  = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post   = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 8',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId = $post->post_id;

        Report::firstOrCreate(
            [
                'reportable_type' => Post::class,
                'reportable_id'   => $postId,
                'reporter_id'     => $buyer->id,
                'status'          => 'pending',
            ],
            ['reason' => 'Post report for moderation test.']
        );

        $browser->loginAs($admin)
            ->visit('/admin/moderation')
            ->assertSee('Moderation Queue')
            ->assertSee('Buyer Test');
    });
});

test('TC.Report.30.010 - Positive (UC): Admin can hide a reported post; it disappears from community feed', function () {
    $this->browse(function (Browser $browser) {
        $admin     = User::where('email', 'admin@rewear.com')->firstOrFail();
        $buyer     = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $seller    = User::where('email', 'seller@rewear.com')->firstOrFail();
        $post      = Post::where('users_id', $seller->id)->first() ?? Post::create([
            'title' => 'Test Post By Seller 9',
            'content' => 'Content of test post',
            'users_id' => $seller->id,
            'upvote_count' => 0,
        ]);
        $postId    = $post->post_id;
        $postTitle = $post->title;

        // Clean state: only one pending report
        Report::where('status', 'pending')->delete();

        Report::create([
            'reportable_type' => Post::class,
            'reportable_id'   => $postId,
            'reporter_id'     => $buyer->id,
            'reason'          => 'Post hide test.',
            'status'          => 'pending',
        ]);

        // Admin hides the post via moderation panel
        $browser->loginAs($admin)
            ->visit('/admin/moderation')
            ->assertSee($postTitle)
            ->press('Hide')
            ->pause(500);

        // Verify post no longer appears to regular user
        $browser->loginAs($buyer)
            ->visit('/community')
            ->assertDontSee($postTitle);
    });
});

// ==========================================
// PBI-31: CO2 SAVINGS TO VOUCHER EXCHANGE
// ==========================================

test('TC.Voucher.31.001 - Positive (UC): User can see the Voucher Exchange section on the profile page', function () {
    $this->browse(function (Browser $browser) {
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();

        $browser->loginAs($seller)
            ->visit('/profile')
            ->assertSee('Voucher Exchange');
    });
});

test('TC.Voucher.31.002 - Positive (UC): User CO2 balance is displayed correctly on profile page', function () {
    $this->browse(function (Browser $browser) {
        // Seller is seeded with total_co2_saved = 35.50 → displayed as "35.5 kg CO₂"
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();

        $browser->loginAs($seller)
            ->visit('/profile')
            ->assertSee('35.5 kg CO');
    });
});

test('TC.Voucher.31.003 - Positive (EP): Only active vouchers with available quantity are shown', function () {
    $this->browse(function (Browser $browser) {
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();

        $codeActive   = 'ACTIVE31' . time();
        $codeInactive = 'INACTIVE31' . time();

        CarbonVoucher::create([
            'code'               => $codeActive,
            'discount_amount'    => 10000,
            'co2_cost'           => 1,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);
        CarbonVoucher::create([
            'code'               => $codeInactive,
            'discount_amount'    => 10000,
            'co2_cost'           => 1,
            'quantity_available' => 5,
            'is_active'          => false,
        ]);

        $browser->loginAs($seller)
            ->visit('/profile')
            ->assertSee($codeActive)
            ->assertDontSee($codeInactive);
    });
});

test('TC.Voucher.31.004 - Positive (BVA): Redeem button enabled when seller CO2 (35.5) >= co2_cost (35)', function () {
    $this->browse(function (Browser $browser) {
        // Seller has 35.50 kg CO2; voucher costs 35 kg → should be affordable
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();

        CarbonVoucher::create([
            'code'               => 'BVA35SEL' . time(),
            'discount_amount'    => 50000,
            'co2_cost'           => 35,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        $browser->loginAs($seller)
            ->visit('/profile')
            ->assertSee('Redeem');
    });
});

test('TC.Voucher.31.005 - Negative (BVA): Redeem button locked when seller CO2 (35.5) < co2_cost (36)', function () {
    $this->browse(function (Browser $browser) {
        // Seller has 35.50 kg CO2; voucher costs 36 kg → should be locked
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();

        CarbonVoucher::create([
            'code'               => 'BVA36SEL' . time(),
            'discount_amount'    => 50000,
            'co2_cost'           => 36,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        $browser->loginAs($seller)
            ->visit('/profile')
            ->assertSee('Locked');
    });
});

test('TC.Voucher.31.006 - Positive (BVA): Redeem button enabled when buyer CO2 (11.5) >= co2_cost (11)', function () {
    $this->browse(function (Browser $browser) {
        // Buyer has 11.50 kg CO2; voucher costs 11 kg → should be affordable
        $buyer = User::where('email', 'buyer@rewear.com')->firstOrFail();

        CarbonVoucher::create([
            'code'               => 'BVA11BUY' . time(),
            'discount_amount'    => 20000,
            'co2_cost'           => 11,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        $browser->loginAs($buyer)
            ->visit('/profile')
            ->assertSee('Redeem');
    });
});

test('TC.Voucher.31.007 - Negative (BVA): Redeem button locked when buyer CO2 (11.5) < co2_cost (12)', function () {
    $this->browse(function (Browser $browser) {
        // Buyer has 11.50 kg CO2; voucher costs 12 kg → should be locked
        $buyer = User::where('email', 'buyer@rewear.com')->firstOrFail();

        CarbonVoucher::create([
            'code'               => 'BVA12BUY' . time(),
            'discount_amount'    => 20000,
            'co2_cost'           => 12,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        $browser->loginAs($buyer)
            ->visit('/profile')
            ->assertSee('Locked');
    });
});

test('TC.Voucher.31.008 - Positive (UC): User successfully redeems a voucher with sufficient CO2', function () {
    $this->browse(function (Browser $browser) {
        // Buyer has 11.50 kg CO2; redeeming a 10 kg cost voucher
        $buyer = User::where('email', 'buyer@rewear.com')->firstOrFail();

        // Reset buyer CO2 to 11.50 in case previous tests changed it
        $buyer->update(['total_co2_saved' => 11.50]);

        // Clean vouchers and redemptions tables to avoid selecting the wrong voucher
        \App\Models\VoucherRedemption::query()->delete();
        CarbonVoucher::query()->delete();

        $code = 'REWEAR10' . time();
        CarbonVoucher::create([
            'code'               => $code,
            'discount_amount'    => 50000,
            'co2_cost'           => 10,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        $browser->loginAs($buyer)
            ->visit('/profile')
            ->assertSee($code)
            ->press('Redeem')
            ->pause(1000)
            ->assertSee($code . ' redeemed');
    });
});

test('TC.Voucher.31.009 - Positive (UC): CO2 balance decrements by co2_cost after voucher redemption', function () {
    $this->browse(function (Browser $browser) {
        // Buyer starts at 11.50 kg, redeems 10 kg cost → remaining 1.5 kg
        $buyer = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $buyer->update(['total_co2_saved' => 11.50]);

        // Clean vouchers and redemptions tables to avoid selecting the wrong voucher
        \App\Models\VoucherRedemption::query()->delete();
        CarbonVoucher::query()->delete();

        $code = 'DEDUCT10' . time();
        CarbonVoucher::create([
            'code'               => $code,
            'discount_amount'    => 30000,
            'co2_cost'           => 10,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        $browser->loginAs($buyer)
            ->visit('/profile')
            ->assertSee('11.5 kg CO')
            ->press('Redeem')
            ->pause(1000)
            ->assertSee('1.5 kg CO');
    });
});

test('TC.Voucher.31.010 - Positive (UC): Redeemed voucher code appears in redemption history modal', function () {
    $this->browse(function (Browser $browser) {
        $buyer = User::where('email', 'buyer@rewear.com')->firstOrFail();
        $buyer->update(['total_co2_saved' => 11.50]);

        // Clean vouchers and redemptions tables to avoid selecting the wrong voucher
        \App\Models\VoucherRedemption::query()->delete();
        CarbonVoucher::query()->delete();

        $code = 'HISTTEST' . time();
        CarbonVoucher::create([
            'code'               => $code,
            'discount_amount'    => 25000,
            'co2_cost'           => 10,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        // Redeem the voucher first
        $browser->loginAs($buyer)
            ->visit('/profile')
            ->press('Redeem')
            ->pause(1000);

        // Open redemption history and verify the code appears
        $browser->loginAs($buyer)
            ->visit('/profile')
            ->click('button[onclick*="redemption-history-modal"]')
            ->waitFor('#redemption-history-modal', 3)
            ->assertSeeIn('#redemption-history-modal', $code);
    });
});

test('TC.Voucher.31.011 - Negative (BVA): Voucher with quantity_available = 0 is not shown on rewards page', function () {
    $this->browse(function (Browser $browser) {
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();

        $code = 'ZEROQTY' . time();
        // Create via DB directly since UI enforces min=1 on creation
        CarbonVoucher::create([
            'code'               => $code,
            'discount_amount'    => 10000,
            'co2_cost'           => 5,
            'quantity_available' => 0,
            'is_active'          => true,
        ]);

        $browser->loginAs($seller)
            ->visit('/profile')
            ->assertDontSee($code);
    });
});

test('TC.Voucher.31.012 - Positive (UC): Admin can access the voucher management page', function () {
    $this->browse(function (Browser $browser) {
        $admin = User::where('email', 'admin@rewear.com')->firstOrFail();

        $browser->loginAs($admin)
            ->visit('/admin/vouchers')
            ->assertSee('Carbon Vouchers');
    });
});

test('TC.Voucher.31.013 - Positive (EP): Admin creates a new voucher with all valid fields', function () {
    $this->browse(function (Browser $browser) {
        $admin = User::where('email', 'admin@rewear.com')->firstOrFail();
        $code  = 'GREENDEAL' . time();

        $browser->loginAs($admin)
            ->visit('/admin/vouchers')
            ->press('New Voucher')
            ->waitFor('#createModal', 3)
            ->type('code', $code)
            ->type('discount_amount', '75000')
            ->type('co2_cost', '20')
            ->type('quantity_available', '10')
            // is_active checkbox is checked by default
            ->press('Create Voucher')
            ->pause(1000)
            ->assertSee($code);
    });
});

test('TC.Voucher.31.014 - Negative (EP): Admin cannot create a voucher with a duplicate code', function () {
    $this->browse(function (Browser $browser) {
        $admin = User::where('email', 'admin@rewear.com')->firstOrFail();
        $code  = 'DUPCODE' . time();

        // Seed a voucher with this code first
        CarbonVoucher::create([
            'code'               => $code,
            'discount_amount'    => 50000,
            'co2_cost'           => 20,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        $browser->loginAs($admin)
            ->visit('/admin/vouchers')
            ->press('New Voucher')
            ->waitFor('#createModal', 3)
            ->type('code', $code)
            ->type('discount_amount', '50000')
            ->type('co2_cost', '20')
            ->type('quantity_available', '5')
            ->press('Create Voucher')
            ->pause(1000)
            ->assertSee('has already been taken');
    });
});

test('TC.Voucher.31.015 - Negative (BVA): Admin cannot create voucher with co2_cost = 0 (minimum is 1)', function () {
    $this->browse(function (Browser $browser) {
        $admin = User::where('email', 'admin@rewear.com')->firstOrFail();

        $browser->loginAs($admin)
            ->visit('/admin/vouchers')
            ->press('New Voucher')
            ->waitFor('#createModal', 3)
            ->type('code', 'ZEROTEST' . time())
            ->type('discount_amount', '10000')
            ->type('co2_cost', '0')
            ->type('quantity_available', '5')
            ->press('Create Voucher')
            // HTML5 min="1" blocks submission; user stays on admin vouchers page
            ->assertPathIs('/admin/vouchers');
    });
});

test('TC.Voucher.31.016 - Positive (BVA): Admin creates voucher with co2_cost = 1 (minimum valid value)', function () {
    $this->browse(function (Browser $browser) {
        $admin = User::where('email', 'admin@rewear.com')->firstOrFail();
        $code  = 'MINVCH' . time();

        $browser->loginAs($admin)
            ->visit('/admin/vouchers')
            ->press('New Voucher')
            ->waitFor('#createModal', 3)
            ->type('code', $code)
            ->type('discount_amount', '5000')
            ->type('co2_cost', '1')
            ->type('quantity_available', '5')
            ->press('Create Voucher')
            ->pause(1000)
            ->assertSee($code);
    });
});

test('TC.Voucher.31.017 - Positive (UC): Updated voucher co2_cost is reflected in admin voucher table', function () {
    $this->browse(function (Browser $browser) {
        // No edit UI exists in admin/vouchers; update via DB and verify table reflects it
        $admin = User::where('email', 'admin@rewear.com')->firstOrFail();

        $voucher = CarbonVoucher::create([
            'code'               => 'UPDATETEST' . time(),
            'discount_amount'    => 50000,
            'co2_cost'           => 20,
            'quantity_available' => 10,
            'is_active'          => true,
        ]);

        // Update co2_cost directly via DB (admin UI for update not yet implemented)
        $voucher->update(['co2_cost' => 25]);

        $browser->loginAs($admin)
            ->visit('/admin/vouchers')
            ->assertSee($voucher->code)
            ->assertSee('25 kg');
    });
});

test('TC.Voucher.31.018 - Positive (UC): Deactivated voucher no longer appears on user rewards page', function () {
    $this->browse(function (Browser $browser) {
        // No toggle UI exists in admin/vouchers; deactivate via DB and verify user-facing effect
        $seller = User::where('email', 'seller@rewear.com')->firstOrFail();

        $code = 'DEACTIVATE' . time();
        $voucher = CarbonVoucher::create([
            'code'               => $code,
            'discount_amount'    => 40000,
            'co2_cost'           => 10,
            'quantity_available' => 5,
            'is_active'          => true,
        ]);

        // Confirm voucher is visible first
        $browser->loginAs($seller)
            ->visit('/profile')
            ->assertSee($code);

        // Deactivate via DB (admin toggle UI not yet implemented in view)
        $voucher->update(['is_active' => false]);

        // Verify it disappears from user rewards page
        $browser->loginAs($seller)
            ->visit('/profile')
            ->assertDontSee($code);
    });
});