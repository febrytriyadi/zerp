<?php

namespace Database\Factories;

use App\Models\Master\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Master\Company::factory(),
            'code' => $this->faker->unique()->bothify('PRD-####'),
            'name' => $this->faker->word(),
            'purchase_price' => $this->faker->numberBetween(1000, 100000),
            'selling_price' => $this->faker->numberBetween(1000, 150000),
            'cost_method' => 'average',
            'average_cost' => 0,
            'is_active' => true,
        ];
    }
}
