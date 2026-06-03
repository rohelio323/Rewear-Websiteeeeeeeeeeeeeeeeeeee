<?php

use App\Models\Post;
use App\Models\User;
use Laravel\Dusk\Browser;

// ==========================================
// PBI-11: VIEW POST
// ==========================================

test('PBI-11 Positive: User can view community page and posts', function () {
    $this->browse(function (Browser $browser) {
        $user = User::first() ?? User::factory()->create();
        $postTitle = 'Test View PBI-11 ' . time();
        
        // Bikin post dummy
        Post::create([
            'title' => $postTitle,
            'content' => 'Konten untuk test otomatis PBI-11',
            'users_id' => $user->id,
            'upvote_count' => 0,
        ]);

        $browser->visit('/community')
                ->assertSee('The Living Archive') // Cek judul halaman
                ->assertSee($postTitle);          // Cek post muncul
    });
});

// ==========================================
// PBI-12: CREATE POST
// ==========================================

test('PBI-12 Positive: User can create a post with valid data', function () {
    $this->browse(function (Browser $browser) {
        $user = User::first() ?? User::factory()->create();
        $newTitle = 'Cerita Thrift Test ' . time();

        $browser->loginAs($user)
                ->visit('/community')
                ->click('div[onclick="openModal(\'createModal\')"]') // Buka modal
                ->waitFor('#createModal', 3) 
                ->type('title', $newTitle)   
                ->type('content', 'Dapat baju vintage murah banget!') 
                ->press('Share to Community') 
                ->assertPathIs('/community')  
                ->assertSee($newTitle); 
    });
});

test('PBI-12 Negative: System rejects post creation if fields are empty', function () {
    $this->browse(function (Browser $browser) {
        $user = User::first() ?? User::factory()->create();

        $browser->loginAs($user)
                ->visit('/community')
                ->click('div[onclick="openModal(\'createModal\')"]')
                ->waitFor('#createModal', 3) 
                // Sengaja dikosongkan
                ->clear('title')
                ->clear('content')
                ->press('Share to Community') 
                // Karena HTML5 "required" menahan form, user tidak akan berpindah halaman
                ->assertPathIs('/community');
    });
});

// ==========================================
// PBI-13: UPDATE & DELETE POST
// ==========================================

test('PBI-13 Positive: User can update and delete their own post', function () {
    $this->browse(function (Browser $browser) {
        $user = User::first() ?? User::factory()->create();
        
        $postToEdit = Post::create([
            'title' => 'Post Untuk Diubah ' . time(),
            'content' => 'Isi awal sebelum diedit',
            'users_id' => $user->id,
            'upvote_count' => 0,
        ]);

        $browser->loginAs($user)
                ->visit('/community')
                ->assertSee($postToEdit->title)
                
                // --- UPDATE ---
                ->click('.kebab-button') 
                ->waitFor('#dropdown-' . $postToEdit->post_id, 2) 
                ->click('#dropdown-' . $postToEdit->post_id . ' button[onclick^="openEditModal"]') 
                ->waitFor('#editModal', 3) 
                ->clear('#editTitle') 
                ->type('#editTitle', 'Judul Sudah Diupdate ' . time()) 
                ->press('Save Changes') 
                ->assertSee('Judul Sudah Diupdate') 

                // --- DELETE ---
                ->click('.kebab-button') 
                ->waitFor('#dropdown-' . $postToEdit->post_id, 2)
                ->press('🗑️ Delete') 
                ->acceptDialog() 
                ->pause(1500) 
                ->assertDontSee('Judul Sudah Diupdate'); 
    });
});

test('PBI-13 Negative: User cannot see edit/delete menu on other users posts', function () {
    $this->browse(function (Browser $browser) {
        $user1 = User::first() ?? User::factory()->create();
        // Bikin akun user kedua
        $user2 = User::factory()->create();

        // Bikin post pakai akun user kedua
        $postOtherUser = Post::create([
            'title' => 'Post Milik User Lain ' . time(),
            'content' => 'Ini post orang lain, tidak boleh diedit.',
            'users_id' => $user2->id,
            'upvote_count' => 0,
        ]);

        // Login pakai akun user kesatu
        $browser->loginAs($user1)
                ->visit('/community')
                ->assertSee($postOtherUser->title)
                // Cek apakah dropdown menu (titik tiga) benar-benar HILANG di post tersebut
                ->assertMissing('#dropdown-' . $postOtherUser->post_id); 
    });
});