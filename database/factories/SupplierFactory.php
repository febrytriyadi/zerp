<?php

namespace Database\Factories;

use App\Models\Master\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Master\Company::factory(),
            'code' => $this->faker->unique()->bothify('SUPP-####'),
            'name' => $this->faker->company(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'is_active' => true,
        ];
    }
}
