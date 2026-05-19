<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WishlistTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        \DB::statement('PRAGMA foreign_keys=OFF;');
    }

    private function createItem(): Item
    {
        $seller = User::factory()->create();

        $category = Category::create([
            'category_name' => 'Tops',
            'co2_constant'  => 2.50,
        ]);

        return Item::create([
            'item_name'   => 'Kaos REwear Dusk',
            'description' => 'Item testing wishlist.',
            'size'        => 'M',
            'condition'   => 'like_new',
            'price'       => 75000,
            'photo_path'  => null,
            'status'      => 'available',
            'users_id'    => $seller->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_add_item_to_wishlist(): void
    {
        $user = User::factory()->create();
        $this->createItem();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/marketplace')
                    ->waitFor('.wishlist-btn', 5)
                    ->click('.wishlist-btn')
                    ->waitForText('Item ditambahkan ke wishlist!', 5)
                    ->assertSee('Item ditambahkan ke wishlist!');
        });
    }

    public function test_view_wishlist_shows_favorited_item(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem();
        $user->favorites()->attach($item->id);

        $this->browse(function (Browser $browser) use ($user, $item) {
            $browser->loginAs($user)
                    ->visit('/favorites')
                    ->waitForText('Saved Items', 5)
                    ->assertSee('Saved Items')
                    ->assertSee($item->item_name);
        });
    }

    public function test_view_empty_wishlist_shows_empty_state(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/favorites')
                    ->waitForText('Saved Items', 5)
                    ->assertSee('Saved Items')
                    ->assertSee('Your wishlist is empty.');
        });
    }

    public function test_remove_item_from_wishlist_via_toggle(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem();
        $user->favorites()->attach($item->id);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/marketplace')
                    ->waitFor('.wishlist-btn', 5)
                    ->click('.wishlist-btn')
                    ->waitForText('Item dihapus dari wishlist.', 5)
                    ->assertSee('Item dihapus dari wishlist.');
        });
    }

    public function test_remove_item_from_favorites_page(): void
    {
        $user = User::factory()->create();
        $item = $this->createItem();
        $user->favorites()->attach($item->id);

        $this->browse(function (Browser $browser) use ($user, $item) {
            $browser->loginAs($user)
                    ->visit('/favorites')
                    ->waitForText($item->item_name, 5)
                    ->assertSee($item->item_name)
                    ->click('.wishlist-btn')
                    ->waitForText('Item dihapus dari wishlist.', 5)
                    ->assertSee('Item dihapus dari wishlist.');
        });
    }

    public function test_guest_cannot_access_favorites_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/favorites')
                    ->waitForLocation('/login', 5)
                    ->assertPathIs('/login');
        });
    }

    public function test_guest_clicking_favorite_redirects_to_login(): void
    {
        $this->createItem();

        $this->browse(function (Browser $browser) {
            $browser->visit('/marketplace')
                    ->waitFor('a[href*="login"]', 5)
                    ->click('a[href*="login"].absolute')
                    ->waitForLocation('/login', 5)
                    ->assertPathIs('/login');
        });
    }
}