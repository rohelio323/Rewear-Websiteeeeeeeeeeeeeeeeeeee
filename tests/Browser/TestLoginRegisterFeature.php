<?php

use App\Models\User;
use Laravel\Dusk\Browser;

// TC.Reg.21.001 | Email Field Input valid email format
test('TC.Reg.21.001 register email field accepts valid email format', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/register')
                ->type('#name', 'Valid User')
                ->type('#email', 'validuser@example.com')
                ->type('#password', 'password123')
                ->check('#terms')
                ->click('button[type="submit"]')
                ->pause(3000)
                ->assertMissing('.input-error');
    });
});

// TC.Reg.21.002 | Email Field Input invalid email format
test('TC.Reg.21.002 register email field rejects invalid email format', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/register')
                ->type('#name', 'Valid User')
                ->type('#email', 'invalidemail')
                ->type('#password', 'password123')
                ->check('#terms')
                ->click('button[type="submit"]')
                ->pause(500);

        $isInvalid = $browser->script('return document.getElementById("email").validity.valid === false')[0];
        expect($isInvalid)->toBeTrue();
    });
});

// TC.Reg.21.003 | Input password below 8 characters (7 chars)
test('TC.Reg.21.003 register password below 8 characters shows notification on submit', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/register')
                ->type('#name', 'Test User')
                ->type('#email', 'test7char@example.com')
                ->type('#password', 'abc1234')
                ->check('#terms')
                ->click('button[type="submit"]')
                ->pause(2000)
                ->assertPresent('#password.input-error'); 
    });
});

// TC.Reg.21.004 | Input password 8 characters
test('TC.Reg.21.004 register password 8 characters', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/register')
                ->type('#name', 'Test User Eight')
                ->type('#email', 'test8char' . uniqid() . '@example.com')
                ->type('#password', 'abcd1234')
                ->check('#terms')
                ->click('button[type="submit"]')
                ->pause(5000)
                ->assertPathIsNot('/register');
    });
});

// TC.Log.21.001 | Email Field Input valid email format
test('TC.Log.21.001 login email field accepts valid email format', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/login')
                ->type('#email', 'user@example.com')
                ->assertInputValue('#email', 'user@example.com');

                $isValid = $browser->script('return document.getElementById("email").validity.valid === true')[0];
        expect($isValid)->toBeTrue();
    });
});

// TC.Log.21.002 | Input invalid email format
test('TC.Log.21.002 login email field rejects invalid email format', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/login')
                ->type('#email', 'notanemail')
                ->type('#password', 'somepassword')
                ->click('button[type="submit"]')
                ->pause(500);

        $isInvalid = $browser->script('return document.getElementById("email").validity.valid === false')[0];
        expect($isInvalid)->toBeTrue();
    });
});

// TC.Reg.21.003 | Password Field Input password below 8 characters (7 chars)
test('TC.Log.21.003 login password below 8 characters shows notification on submit', function () {
    $email = 'logintest' . uniqid() . '@example.com';
    User::factory()->create([
        'email'    => $email,
        'password' => bcrypt('correctpassword123'),
    ]);

    $this->browse(function (Browser $browser) use ($email) {
        $browser->logout()->visit('/login')
                ->type('#email', $email)
                ->type('#password', 'short12')
                ->click('button[type="submit"]')
                ->pause(2000)
                ->assertPathIs('/login'); 
    });
});

// TC.Reg.21.004 | Input password 8 or more characters, login success
test('TC.Log.21.004 login password 8 or above characters with correct credentials succeeds', function () {
    $email = 'login8char' . uniqid() . '@example.com';
    User::factory()->create([
        'email'    => $email,
        'password' => bcrypt('password8'),
    ]);

    $this->browse(function (Browser $browser) use ($email) {
        $browser->logout()->visit('/login')
                ->type('#email', $email)
                ->type('#password', 'password8')
                ->click('button[type="submit"]')
                ->pause(4000)
                ->assertPathIsNot('/login');
    });
});

// TC.Log.21.05 | Input wrong password
test('TC.Log.21.005 login with wrong password shows error', function () {
    $email = 'wrongpass' . uniqid() . '@example.com';
    User::factory()->create([
        'email'    => $email,
        'password' => bcrypt('correctpassword'),
    ]);

    $this->browse(function (Browser $browser) use ($email) {
        $browser->logout()->visit('/login')
                ->type('#email', $email)
                ->type('#password', 'wrongpassword')
                ->click('button[type="submit"]')
                ->pause(3000)
                ->assertSee('credentials');
    });
});

// TC.Log.001.05 | Login without any field filled
test('TC.Log.21.05 login without any field filled shows required validation', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/login')
                ->click('button[type="submit"]')
                ->pause(500);
        $isMissing = $browser->script('return document.getElementById("email").validity.valueMissing === true')[0];
        expect($isMissing)->toBeTrue();
    });
});

// TC.Out.21.001 | Authenticated user can logout successfully
test('TC.Out.21.001 authenticated user can logout successfully', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/')
                ->pause(1000)
                ->click('form[action*="logout"] button[type="submit"]')
                ->pause(2000)
                ->assertPathIs('/');
    });
});