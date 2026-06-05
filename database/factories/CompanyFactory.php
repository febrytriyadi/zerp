<?php

namespace Database\Factories;

use App\Models\Master\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('CMP-####'),
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'tax_id' => $this->faker->numerify('###########'),
            'is_active' => true,
        ];
    }
}
