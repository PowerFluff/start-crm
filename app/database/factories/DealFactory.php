<?php

namespace Database\Factories;

use App\Enums\DealStatus;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title' => fake()->sentence(3),
            'amount' => fake()->numberBetween(10000, 500000),
            'status' => fake()->randomElement(DealStatus::values()),
            'expected_close_date' => fake()->date(),
        ];
    }
}