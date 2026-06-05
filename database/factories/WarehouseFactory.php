<?php

namespace Database\Factories;

use App\Models\Master\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Master\Company::factory(),
            'code' => $this->faker->unique()->bothify('WH-####'),
            'name' => $this->faker->word() . ' Warehouse',
            'address' => $this->faker->address(),
            'is_active' => true,
        ];
    }
}
