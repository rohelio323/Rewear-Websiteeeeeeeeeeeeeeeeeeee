<?php

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;

function makeSeller(): User
{
    return User::factory()->create(['is_verified_seller' => true]);
}

function makeItem(User $seller, string $name = 'Test Vintage Jacket'): Item
{
    $category = Category::first() ?? Category::factory()->create();

    $storedPath = Storage::disk('public')->putFileAs(
        'items',
        new \Illuminate\Http\File(public_path('placeholder.jpg')),
        'placeholder_test_' . uniqid() . '.jpg'
    );

    return Item::create([
        'item_name'   => $name,
        'description' => 'A test item for Dusk.',
        'size'        => 'L',
        'condition'   => 'good',
        'price'       => 150000,
        'category_id' => $category->id,
        'users_id'    => $seller->id,
        'photo_path'  => [$storedPath],
        'status'      => 'available',
    ]);
}

//  PBI-04 — Marketplace Listing

// TC.Item.04.001 | View marketplace page as guest
test('TC.Item.04.001 view marketplace page as guest', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/marketplace')
                ->assertSee('Marketplace');
    });
});

// TC.Item.04.002 | Item cards display required info
test('TC.Item.04.002 item cards show photo, title, price, and condition', function () {
    $seller = makeSeller();
    makeItem($seller, 'Vintage Dusk Jacket');

    $this->browse(function (Browser $browser) {
        $browser->visit('/marketplace')
                ->assertSee('Vintage Dusk Jacket')
                ->assertSee('Rp');
    });
});

// TC.Item.04.003 | Only available items are shown
test('TC.Item.04.003 only available items are displayed', function () {
    $seller   = makeSeller();
    $category = Category::first() ?? Category::factory()->create();

    $storedPath = Storage::disk('public')->putFileAs(
        'items', new \Illuminate\Http\File(public_path('placeholder.jpg')), 'ph_avail_' . uniqid() . '.jpg'
    );

    Item::create([
        'item_name' => 'Available Test Item', 
        'description' => 'ok', 
        'size' => 'M',
        'condition' => 'good', 
        'price' => 100000, 
        'category_id' => $category->id,
        'users_id' => $seller->id, 
        'photo_path' => [$storedPath], 
        'status' => 'available',
    ]);
    Item::create([
        'item_name' => 'Sold Test Item', 
        'description' => 'ok', 
        'size' => 'M',
        'condition' => 'good', 
        'price' => 100000, 
        'category_id' => $category->id,
        'users_id' => $seller->id, 
        'photo_path' => [$storedPath], 
        'status' => 'sold',
    ]);

    $this->browse(function (Browser $browser) {
        $browser->visit('/marketplace')
                ->assertSee('Available Test Item')
                ->assertDontSee('Sold Test Item');
    });
});

//  PBI-05 — Create Item Listing

// TC.Item.05.001 | Seller accesses create form
test('TC.Item.05.001 seller can open the item creation form', function () {
    $seller = makeSeller();

    $this->browse(function (Browser $browser) use ($seller) {
        $browser->loginAs($seller)
                ->visit('/items/create')
                ->assertSee('The Living Archive')
                ->assertVisible('#listing-form');
    });
});

// TC.Item.05.002 | Submit form with all valid data
test('TC.Item.05.002 submit item form with all valid data creates item', function () {
    $seller    = makeSeller();
    $imagePath = public_path('placeholder.jpg');

    $this->browse(function (Browser $browser) use ($seller, $imagePath) {
        $browser->loginAs($seller)
                ->visit('/items/create');

        $browser->type('item_name', '1990s Oversized Linen Blazer')
                ->select('category_id', Category::first()->id)
                ->type('description', 'Kondisi masih sangat bagus.')
                ->type('size', 'L')
                ->select('condition', 'good')
                ->type('price', 350000)
                ->attach('#photo_input', $imagePath)
                ->script('const f=document.getElementById("photo_input").files[0];if(f){slotFiles[0]=f;renderSlot(0,f);}');

        $browser->pause(500)
                ->press('Confirm Listing')
                ->pause(8000)
                ->assertPathIsNot('/items/create');
    });
});

// TC.Item.05.003 | Submit with empty item name
test('TC.Item.05.003 submit form with empty name shows validation error', function () {
    $seller    = makeSeller();
    $imagePath = public_path('placeholder.jpg');

    $this->browse(function (Browser $browser) use ($seller, $imagePath) {
        $browser->loginAs($seller)
                ->visit('/items/create');

        $browser->attach('#photo_input', $imagePath)
                ->script('const f=document.getElementById("photo_input").files[0];if(f){slotFiles[0]=f;renderSlot(0,f);}');

        $browser->select('category_id', Category::first()->id)
                ->type('description', 'Deskripsi contoh.')
                ->type('size', 'M')
                ->select('condition', 'good')
                ->type('price', 100000)
                ->pause(300)
                ->press('Confirm Listing')
                ->pause(3000)
                ->assertSee('item name');
    });
});

// TC.Item.05.004 | Submit with price = 0 (below minimum)
test('TC.Item.05.004 submit with price 0 shows validation error', function () {
    $seller    = makeSeller();
    $imagePath = public_path('placeholder.jpg');

    $this->browse(function (Browser $browser) use ($seller, $imagePath) {
        $browser->loginAs($seller)
                ->visit('/items/create');

        $browser->attach('#photo_input', $imagePath)
                ->script('const f=document.getElementById("photo_input").files[0];if(f){slotFiles[0]=f;renderSlot(0,f);}');

        $browser->type('item_name', 'Zero Price Item')
                ->select('category_id', Category::first()->id)
                ->type('description', 'Deskripsi.')
                ->type('size', 'M')
                ->select('condition', 'good')
                ->type('price', 0)
                ->pause(300)
                ->press('Confirm Listing')
                ->pause(3000)
                ->assertSee('price');
    });
});

// TC.Item.05.005 | Submit with price = 1 (minimum valid)
test('TC.Item.05.005 submit with minimum valid price 1 creates item', function () {
    $seller    = makeSeller();
    $imagePath = public_path('placeholder.jpg');

    $this->browse(function (Browser $browser) use ($seller, $imagePath) {
        $browser->loginAs($seller)
                ->visit('/items/create');

        $browser->attach('#photo_input', $imagePath)
                ->script('const f=document.getElementById("photo_input").files[0];if(f){slotFiles[0]=f;renderSlot(0,f);}');

        $browser->type('item_name', 'Min Price Item')
                ->select('category_id', Category::first()->id)
                ->type('description', 'Deskripsi.')
                ->type('size', 'S')
                ->select('condition', 'fair')
                ->type('price', 1)
                ->pause(300)
                ->press('Confirm Listing')
                ->pause(8000)
                ->assertPathIsNot('/items/create');
    });
});

// TC.Item.05.006 | Submit with negative price
test('TC.Item.05.006 submit with negative price shows validation error', function () {
    $seller    = makeSeller();
    $imagePath = public_path('placeholder.jpg');

    $this->browse(function (Browser $browser) use ($seller, $imagePath) {
        $browser->loginAs($seller)
                ->visit('/items/create');

        $browser->attach('#photo_input', $imagePath)
                ->script('const f=document.getElementById("photo_input").files[0];if(f){slotFiles[0]=f;renderSlot(0,f);}');

        $browser->type('item_name', 'Negative Price Item')
                ->select('category_id', Category::first()->id)
                ->type('description', 'Deskripsi.')
                ->type('size', 'S')
                ->select('condition', 'fair')
                ->type('price', -50)
                ->pause(300)
                ->press('Confirm Listing')
                ->pause(3000)
                ->assertSee('price');
    });
});

// TC.Item.05.007 | Upload non-image file
test('TC.Item.05.007 upload non-image file shows validation error', function () {
    $seller  = makeSeller();
    $fakePdf = tempnam(sys_get_temp_dir(), 'test') . '.pdf';
    file_put_contents($fakePdf, '%PDF fake');

    $this->browse(function (Browser $browser) use ($seller, $fakePdf) {
        $browser->loginAs($seller)
                ->visit('/items/create');

        $browser->attach('#photo_input', $fakePdf)
                ->script('const f=document.getElementById("photo_input").files[0];if(f){slotFiles[0]=f;renderSlot(0,f);}');

        $browser->type('item_name', 'PDF Upload Item')
                ->select('category_id', Category::first()->id)
                ->type('description', 'Deskripsi.')
                ->type('size', 'M')
                ->select('condition', 'good')
                ->type('price', 50000)
                ->pause(300)
                ->press('Confirm Listing')
                ->pause(3000)
                ->assertSee('image');
    });

    @unlink($fakePdf);
});

// TC.Item.05.008 | Upload 1 photo (minimum boundary)
test('TC.Item.05.008 upload exactly 1 photo creates item successfully', function () {
    $seller    = makeSeller();
    $imagePath = public_path('placeholder.jpg');

    $this->browse(function (Browser $browser) use ($seller, $imagePath) {
        $browser->loginAs($seller)
                ->visit('/items/create')
                ->script('document.getElementById("photo_input").classList.remove("hidden");');

        $browser->attach('#photo_input', $imagePath)
                ->script('const f=document.getElementById("photo_input").files[0];if(f){slotFiles[0]=f;renderSlot(0,f);}');

        $browser->type('item_name', 'One Photo Item')
                ->select('category_id', Category::first()->id)
                ->type('description', 'Item with one photo.')
                ->type('size', 'M')
                ->select('condition', 'like_new')
                ->type('price', 75000)
                ->pause(300)
                ->press('Confirm Listing')
                ->pause(8000)
                ->assertPathIsNot('/items/create');
    });
});

// TC.Item.05.009 | Upload 4 photos (maximum boundary)
test('TC.Item.05.009 upload exactly 4 photos creates item successfully', function () {
    $seller    = makeSeller();
    $imagePath = public_path('placeholder.jpg');

    $this->browse(function (Browser $browser) use ($seller, $imagePath) {
        $browser->loginAs($seller)
                ->visit('/items/create')
                ->script('document.getElementById("photo_input").classList.remove("hidden");');

        $browser->attach('#photo_input', $imagePath)
                ->script('
                    const f = document.getElementById("photo_input").files[0];
                    if (f) {
                        for (let i = 0; i < 4; i++) {
                            slotFiles[i] = f;
                            renderSlot(i, f);
                        }
                    }
                ');

        $browser->type('item_name', 'Four Photos Item')
                ->select('category_id', Category::first()->id)
                ->type('description', 'Item with four photos.')
                ->type('size', 'L')
                ->select('condition', 'good')
                ->type('price', 200000)
                ->pause(300)
                ->press('Confirm Listing')
                ->pause(8000)
                ->assertPathIsNot('/items/create');
    });
});

// TC.Item.05.010 | Unauthenticated user redirected to login
test('TC.Item.05.010 unauthenticated user is redirected to login from create form', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()
                ->visit('/items/create')
                ->assertPathIs('/login');
    });
});

//  PBI-06 — Item Detail Page

// TC.Item.06.001 | View item detail page
test('TC.Item.06.001 item detail page shows all required information', function () {
    $seller = makeSeller();
    $item   = makeItem($seller);

    $this->browse(function (Browser $browser) use ($item) {
        $browser->visit('/item/detail/' . $item->id)
                ->assertSee($item->item_name)
                ->assertSee($item->description)
                ->assertSee($item->size);
    });
});

// TC.Item.06.002 | Access detail of non-existent item returns 404
test('TC.Item.06.002 accessing non-existent item shows 404 page', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/item/detail/99999')
                ->assertSee('404');
    });
});

// TC.Item.06.003 | Edit and delete buttons visible to owner
test('TC.Item.06.003 edit and delete buttons are visible to the item owner', function () {
    $seller = makeSeller();
    $item   = makeItem($seller);

    $this->browse(function (Browser $browser) use ($seller, $item) {
        $browser->loginAs($seller)
                ->visit('/item/detail/' . $item->id)                
                ->assertPresent('a[href*="' . $item->id . '/edit"]')
                ->assertPresent('form[action*="' . $item->id . '"]');
    });
});

//  PBI-07 — Edit & Delete Item

// TC.Item.07.001 | Owner accesses edit form with pre-filled data
test('TC.Item.07.001 owner can open edit form with pre-filled data', function () {
    $seller = makeSeller();
    $item   = makeItem($seller);

    $this->browse(function (Browser $browser) use ($seller, $item) {
        $browser->loginAs($seller)
                ->visit('/items/' . $item->id . '/edit')
                ->assertInputValue('item_name', $item->item_name);
    });
});

// TC.Item.07.002 | Edit item with valid data
test('TC.Item.07.002 owner can update item with valid data', function () {
    $seller = makeSeller();
    $item   = makeItem($seller);

    $this->browse(function (Browser $browser) use ($seller, $item) {
        $browser->loginAs($seller)
                ->visit('/items/' . $item->id . '/edit')
                ->clear('item_name')
                ->type('item_name', 'Updated Jacket Name')
                ->press('Update Listing')
                ->pause(5000)
                ->assertSee('Updated Jacket Name');
    });
});

// TC.Item.07.003 | Edit item with empty name shows validation error
test('TC.Item.07.003 clearing item name on edit shows validation error', function () {
    $seller = makeSeller();
    $item   = makeItem($seller);

    $this->browse(function (Browser $browser) use ($seller, $item) {
        $browser->loginAs($seller)
                ->visit('/items/' . $item->id . '/edit')
                ->clear('item_name')
                ->press('Update Listing')
                ->pause(3000)
                ->assertSee('item name');
    });
});

// TC.Item.07.004 | Edit price to -1 (negative, invalid)
test('TC.Item.07.004 setting price to negative value on edit shows validation error', function () {
    $seller = makeSeller();
    $item   = makeItem($seller);

    $this->browse(function (Browser $browser) use ($seller, $item) {
        $browser->loginAs($seller)
                ->visit('/items/' . $item->id . '/edit')
                ->clear('price')
                ->type('price', -1)
                ->press('Update Listing')
                ->pause(3000)
                ->assertSee('price');
    });
});

// TC.Item.07.005 | Non-owner cannot access edit form
test('TC.Item.07.005 non-owner is forbidden from accessing the edit form', function () {
    $seller = makeSeller();
    $other  = makeSeller();
    $item   = makeItem($seller);

    $this->browse(function (Browser $browser) use ($other, $item) {
        $browser->loginAs($other)
                ->visit('/items/' . $item->id . '/edit')
                ->assertSee('403');
    });
});

// TC.Item.07.006 | Owner can delete own item
test('TC.Item.07.006 owner can delete their own item', function () {
    $seller = makeSeller();
    $uniqueName = 'Unique Jacket ' . uniqid();
    $item       = makeItem($seller, $uniqueName);

    $this->browse(function (Browser $browser) use ($seller, $item) {
        $browser->loginAs($seller)
                ->visit('/items/' . $item->id . '/edit')
                ->click('form[action*="' . $item->id . '"] button[type="submit"]')
                ->pause(1000)
                ->acceptDialog()
                ->pause(5000)
                ->assertPathIs('/marketplace')
                ->assertDontSee($item->item_name);
    });
});