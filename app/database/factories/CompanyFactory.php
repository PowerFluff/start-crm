<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;


/**
 * @extends Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'name' => fake()->company(),
            'website' => fake()->url(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}