<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name'               => 'Admin ReWear',
            'email'              => 'admin@rewear.com',
            'password'           => Hash::make('password'),
            'role'               => 'admin',
            'is_verified_seller' => true,
            'total_co2_saved'    => 0.00,
        ]);

        // Test seller (verified)
        User::create([
            'name'               => 'Seller Test',
            'email'              => 'seller@rewear.com',
            'password'           => Hash::make('password'),
            'role'               => 'user',
            'is_verified_seller' => true,
            'total_co2_saved'    => 35.50,
        ]);

        // Test buyer
        User::create([
            'name'               => 'Buyer Test',
            'email'              => 'buyer@rewear.com',
            'password'           => Hash::make('password'),
            'role'               => 'user',
            'is_verified_seller' => false,
            'total_co2_saved'    => 11.50,
        ]);

        $this->call([
            CategorySeeder::class,
            ItemSeeder::class,
            ChallengeSeeder::class,
            PostSeeder::class,
            PostVoteSeeder::class,
        ]);
    }

}
