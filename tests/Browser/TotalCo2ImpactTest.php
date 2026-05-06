<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TotalCo2ImpactTest extends DuskTestCase
{
    /**
     * Use Truncation instead of Migrations. 
     * This empties the tables between tests WITHOUT dropping the schema,
     * which prevents Foreign Key constraint errors and runs much faster.
     */
    use DatabaseTruncation;

    /**
     * TC.CO2.03.001: View Personal Total CO2 on Profile
     */
    public function test_user_sees_lifetime_co2_impact_on_profile(): void
    {
        // 1. Setup the User with specific CO2 saved
        $user = User::forceCreate([
            'name' => 'Eco Buyer',
            'email' => 'ecobuyer@rewear.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'total_co2_saved' => 45.50 
        ]);

        // 2. Run the Browser Test
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->pause(1000)
                    ->screenshot('user_profile_impact_pass')
                    ->assertSee('Account Settings')
                    ->assertSee('LIFETIME IMPACT')
                    ->assertSee('45.50'); 
        });
    }

    /**
     * TC.CO2.03.002: View Platform Total CO2 on Admin Dashboard
     */
    public function test_admin_sees_platform_total_co2_on_dashboard(): void
    {
        // 1. Setup Admin with 35,000 kg (which equals 35.0 Tons on the dashboard)
        $admin = User::forceCreate([
            'name' => 'Admin Boss',
            'email' => 'admin@rewear.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'total_co2_saved' => 35000.00 
        ]);

        // 2. Run the Browser Test
        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->waitForText('35.0', 5)
                    ->screenshot('admin_dashboard_tons_pass')
                    ->assertSee('35.0'); 
        });
    }
}