<?php

namespace Database\Factories;

use App\Models\Master\ChartOfAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChartOfAccountFactory extends Factory
{
    protected $model = ChartOfAccount::class;

    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Master\Company::factory(),
            'code' => $this->faker->unique()->bothify('#-####'),
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement(['asset', 'liability', 'equity', 'revenue', 'expense']),
            'normal_balance' => $this->faker->randomElement(['debit', 'credit']),
            'is_header' => false,
            'is_active' => true,
            'level' => 1,
            'balance' => 0,
        ];
    }
}
