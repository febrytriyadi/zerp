<?php

namespace Database\Factories;

use App\Models\Master\FiscalPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class FiscalPeriodFactory extends Factory
{
    protected $model = FiscalPeriod::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Master\Company::factory(),
            'code' => $this->faker->unique()->bothify('FP-####'),
            'name' => $this->faker->word(),
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'is_open' => true,
            'is_closed' => false,
        ];
    }
}
