<?php

use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Order;

test('TC.Order.14.001 a buyer can create a pending order using the buy now button', function () {
    $imagePath = public_path('placeholder.jpg');

    $this->browse(function (Browser $browser) use ($imagePath) {
        $browser->loginAs(User::find(1))
                ->visit('/item/detail/1')
                ->press('Buy Now')
                ->pause(2000)
                ->waitForLocation('/orders/1', 15)
                ->assertPathIs('/orders/1')
                ->waitForText('Confirm Payment', 10)
                ->click('a[href*="payment"]')  
                ->assertPathIs('/orders/1/payment')
                ->waitFor('input[name="bank_name"]', 5)
                ->type('bank_name', 'BCA')
                ->type('payment_reference', '1234567890')
                ->attach('#payment_proof', $imagePath)
                ->press('Confirm Payment')
                ->waitForText('Order Confirmed', 15) 
                ->assertSee('Order Confirmed');
    });
});

test('TC.Order.14.002 a seller cannot buy their own item listing', function () {
    $this->browse(function (Browser $browser) {
        $sellerItem = \App\Models\Item::where('users_id', 2)->first();

        $browser->loginAs(User::find(2))
                ->visit('/item/detail/' . $sellerItem->id)
                ->press('Buy Now')
                ->assertPathIs('/item/detail/' . $sellerItem->id);
    });
});

test('TC.Order.15.003 a buyer can confirm payment with valid details', function () {

    $imagePath = public_path('placeholder.jpg');

    $item = \App\Models\Item::first();

    $order = Order::create([
        'buyer_id' => 1,
        'item_id' => $item->id,
        'users_id' => $item->users_id,
        'status' => 'pending',
        'total_price' => 100000,
    ]);

    $this->browse(function (Browser $browser) use ($imagePath, $order) {

        $browser->loginAs(User::find(1))
                ->visit('/orders/' . $order->id . '/payment')
                ->type('bank_name', 'BCA')
                ->type('payment_reference', '1234567890')
                ->attach('payment_proof', $imagePath)
                ->press('Confirm Payment');
    });
});

test('TC.Order.15.004 a buyer cannot confirm payment with empty fields', function () {

    $item = \App\Models\Item::first();

    $order = Order::create([
        'buyer_id' => 1,
        'item_id' => $item->id,
        'users_id' => $item->users_id,
        'status' => 'pending',
        'total_price' => 100000,
    ]);

    $this->browse(function (Browser $browser) use ($order) {

        $browser->loginAs(User::find(1))
                ->visit('/orders/' . $order->id . '/payment')
                ->press('Confirm Payment')
                ->assertPathIs('/orders/' . $order->id . '/payment');
    });
});

test('TC.Order.16.005 a seller can confirm shipment with valid details', function () {

    $imagePath = public_path('placeholder.jpg');

    $item = \App\Models\Item::first();

    $order = Order::create([
        'buyer_id' => 1,
        'item_id' => $item->id,
        'users_id' => 2,
        'status' => 'payment_confirmed',
        'total_price' => 100000,
    ]);

    $this->browse(function (Browser $browser) use ($imagePath, $order) {

        $browser->loginAs(User::find(2))
                ->visit('/orders/' . $order->id)

                ->waitForText('Mark as Shipped', 5)
                ->press('Mark as Shipped →')

                ->pause(2000)

                ->waitFor('#ship-form', 10)

                ->click('input[name="courier_name"]')
                ->type('courier_name', 'JNE')

                ->click('input[name="tracking_number"]')
                ->type('tracking_number', 'JNE12345')

                ->attach('shipping_proof', $imagePath)

                ->pause(1000)

                ->press('Confirm Shipment')

                ->pause(3000)

                ->assertSee('Back to Marketplace');
    });
});

test('TC.Order.16.006 a seller cannot confirm shipment without shipping proof', function () {

    $item = \App\Models\Item::first();

    $order = Order::create([
        'buyer_id' => 1,
        'item_id' => $item->id,
        'users_id' => 2,
        'status' => 'payment_confirmed',
        'total_price' => 100000,
    ]);

    $this->browse(function (Browser $browser) use ($order) {

        $browser->loginAs(User::find(2))
                ->visit('/orders/' . $order->id)
                ->press('Mark as Shipped →')
                ->waitFor('#ship-form', 5)
                ->type('courier_name', 'JNE')
                ->type('tracking_number', 'JNE12345')
                ->press('Confirm Shipment')
                ->assertPathIs('/orders/' . $order->id);
    });
});

test('TC.Order.17.007 a buyer can confirm order received and order becomes completed', function () {

    $item = \App\Models\Item::first();

    $order = Order::create([
        'buyer_id' => 1,
        'item_id' => $item->id,
        'users_id' => 2,
        'status' => 'shipped',
        'total_price' => 100000,
    ]);

    $this->browse(function (Browser $browser) use ($order) {

        $browser->loginAs(User::find(1))
                ->visit('/orders/' . $order->id)

                ->waitForText('Confirm Received', 5)

                ->press('Confirm Received')

                ->pause(3000)

                ->refresh()

                ->screenshot('after-confirm')

                ->assertSee('Order Confirmed');
    });

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'completed',
    ]);
});

test('TC.Order.17.008 a buyer can cancel a pending order', function () {

    $item = \App\Models\Item::first();

    $order = Order::create([
        'buyer_id' => 1,
        'item_id' => $item->id,
        'users_id' => 2,
        'status' => 'pending',
        'total_price' => 100000,
    ]);

    $this->browse(function (Browser $browser) use ($order) {

        $browser->loginAs(User::find(1))
                ->visit('/orders/' . $order->id)
                ->press('Cancel Order')
                ->waitForLocation('/marketplace', 5)
                ->assertPathIs('/marketplace');
    });
});

test('TC.Trans.17.001 a buyer can view their purchases in transaction history', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::find(1))
                ->visit('/transactions')
                ->assertPathIs('/transactions')
                ->assertSee('My Orders')
                ->assertSee('Purchases');
    });
});

test('TC.Trans.17.002 a seller can view their sales in transaction history', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::find(2))
                ->visit('/transactions')
                ->assertPathIs('/transactions')
                ->assertSee('My Orders')
                ->assertSee('Sales');
    });
});

test('TC.Trans.17.003 an unauthenticated user cannot access the transaction history page', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()
                ->visit('/transactions')
                ->assertPathIsNot('/transactions');
    });
});