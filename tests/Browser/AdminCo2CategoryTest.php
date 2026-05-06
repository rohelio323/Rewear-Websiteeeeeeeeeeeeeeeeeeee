<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminCo2CategoryTest extends DuskTestCase
{
    /**
     * Use Truncation instead of Migrations for faster execution
     * and to avoid foreign key constraint errors.
     */
    use DatabaseTruncation; 

    /**
     * TC.CO2.01.001: Positive Test - Create Category
     */
    public function test_admin_can_successfully_create_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/co2-categories')
                    ->assertSee('CO₂ Categories')
                    ->press('Add Category')
                    ->waitForText('Define a new item type and its impact.', 3)
                    // Input valid data
                    ->type('category_name', 'Winter Coats')
                    ->type('co2_constant', '15.50')
                    ->press('Save Category')
                    // Verify success
                    ->waitForText('defined successfully', 5)
                    ->assertSee('Winter Coats')
                    ->assertSee('15.50 kg')
                    ->screenshot('admin_create_category_pass'); 
        });
    }

    /**
     * TC.CO2.01.002: Negative Test - Blank CO2 Constant
     */
    public function test_admin_cannot_create_category_with_blank_co2(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/co2-categories')
                    ->press('Add Category')
                    ->waitForText('Define a new item type', 3)
                    
                    // Input name, but leave CO2 blank
                    ->type('category_name', 'Scarves')
                    ->clear('co2_constant') 
                    ->press('Save Category')
                    
                    // 1. Pause to let Chrome block the form
                    ->pause(1000) 
                    ->screenshot('admin_create_category_validation_pass') 
                    
                    // 2. Verify the success message never appears
                    ->assertDontSee('Category added')
                    
                    // 3. Verify the modal is still open
                    ->assertSee('Define a new item type'); 
        });
    }

    /**
     * TC.CO2.01.003: Edit Test - Update Existing Category
     */
    public function test_admin_can_edit_existing_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = \App\Models\Category::forceCreate([
            'category_name' => 'Old Shirts',
            'co2_constant' => 10.00
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/co2-categories')
                    ->waitForText('Old Shirts', 5)
                    
                    // 1. Click the edit button
                    ->script("document.querySelector('td button[type=\"button\"]').click();");

            // 2. Wait for the modal and the animation
            $browser->waitForText('Edit Impact Value', 5)
                    ->pause(1000) 
                    
                    // 3. Target Specifically by the x-model attribute to avoid the hidden 'Add Modal' input conflict
                    ->type('input[x-model="editCo2"]', '50.00')
                    
                    // 4. Click the Update button
                    ->press('Update Value') 
                    
                    // 5. Verify success
                    ->waitForText('50.00 kg', 5) 
                    ->assertSee('50.00 kg')
                    ->screenshot('admin_edit_category_pass'); 
        });
    }

    /**
     * TC.CO2.01.004: Delete Category Test
     */
    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create the item to be deleted
        $category = \App\Models\Category::forceCreate([
            'category_name' => 'Delete Me',
            'co2_constant' => 99.99
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/co2-categories')
                    ->waitForText('Delete Me', 5)
                    
                    // 1. Make the delete button to click via JavaScript
                    ->script("document.querySelector('button.hover\\\\:text-red-600').click();");

            // 2. Wait 1 second for the browser alert to physically render
            $browser->pause(1000)
                    ->acceptDialog() 
                    
                    // 3. Verify that it disappeared
                    ->waitForText('Category deleted', 5)
                    ->assertDontSee('Delete Me')
                    ->screenshot('admin_delete_category_pass'); 
        });
    }
}