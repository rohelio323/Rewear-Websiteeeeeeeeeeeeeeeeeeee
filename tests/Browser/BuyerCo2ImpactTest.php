<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Hash;

class BuyerCo2ImpactTest extends DuskTestCase
{
    /**
     * Use Truncation instead of Migrations for faster execution
     * and to avoid foreign key constraint errors.
     */
    use DatabaseTruncation;

    /**
     * TC.CO2.005: View CO2 on Order Confirmation
     */
    public function test_buyer_sees_co2_impact_on_order_confirmation(): void
    {
        // 1. Setup Buyer 
        $buyer = User::forceCreate([
            'name' => 'Buyer Test',
            'email' => 'buyer1@rewear.com',
            'password' => Hash::make('password'),
            'role' => 'user' 
        ]);

        // 2. Setup Category
        $category = Category::forceCreate([
            'id' => 999, 
            'category_name' => 'T-Shirts',
            'co2_constant' => 10.50
        ]);

        // 3. Setup Item 
        $item = Item::forceCreate([
            'users_id'    => $buyer->id, 
            'category_id' => 999, 
            'item_name'   => 'Retro 90s Oversized T-Shirt',
            'description' => 'Genuine Knitted Denim Leather T-Shirt',
            'size'        => 'L',
            'condition'   => 'good',
            'price'       => 50000,
            'status'      => 'sold',
        ]);

        // 4. Setup Order
        $order = Order::forceCreate([
            'buyer_id'    => $buyer->id, 
            'users_id'    => $buyer->id, 
            'item_id'     => $item->id,
            'total_price' => 50000,
            'status'      => 'completed',
            'co2_saved_amount' => 10.50 
        ]);

        // 5. Run the Browser Test
        $this->browse(function (Browser $browser) use ($buyer, $order) {
            $browser->loginAs($buyer)
                    ->visitRoute('orders.confirmed', ['order' => $order->id])
                    ->waitForText('10.5', 5) 
                    ->screenshot('buyer_order_confirmation_pass') 
                    ->assertSee('Order Confirmed')
                    ->assertSee('Retro 90s Oversized T-Shirt') 
                    ->assertSee('YOUR IMPACT')
                    ->assertSee('10.5'); 
        });
    }
}