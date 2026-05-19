<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'category_name' => $this->faker->randomElement([
                'Tops', 'Bottoms', 'Outerwear', 'Dresses', 'Shoes', 'Accessories'
            ]),
            'co2_constant' => $this->faker->randomFloat(2, 1.0, 10.0),
        ];
    }
}