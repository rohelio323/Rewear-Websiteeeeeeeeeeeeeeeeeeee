<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@rewear.com'], [
            'name'               => 'Admin ReWear',
            'password'           => Hash::make('password'),
            'role'               => 'admin',
            'is_verified_seller' => true,
            'total_co2_saved'    => 0.00,
        ]);

        User::firstOrCreate(['email' => 'seller@rewear.com'], [
            'name'               => 'Seller Test',
            'password'           => Hash::make('password'),
            'role'               => 'user',
            'is_verified_seller' => true,
            'total_co2_saved'    => 35.50,
        ]);

        User::firstOrCreate(['email' => 'buyer@rewear.com'], [
            'name'               => 'Buyer Test',
            'password'           => Hash::make('password'),
            'role'               => 'user',
            'is_verified_seller' => false,
            'total_co2_saved'    => 11.50,
        ]);

        $this->call([
            CategorySeeder::class,
            ItemSeeder::class,
            ChallengeSeeder::class,
        ]);
    }

}
