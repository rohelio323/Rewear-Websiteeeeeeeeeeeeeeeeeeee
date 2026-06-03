<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'item_name'   => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'size'        => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'condition'   => $this->faker->randomElement([
                'new_with_tags', 'like_new', 'good', 'fair'
            ]),
            'price'       => $this->faker->numberBetween(25000, 500000),
            'photo_path'  => null,
            'status'      => 'available',
            'users_id'    => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}