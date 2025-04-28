<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 20),
            'category_id' => fake()->numberBetween(1, 20),
            'max_amount' => fake()->numberBetween(100, 1000),
            'frequency' => fake()->randomElement(['monthly', 'weekly', 'daily']),
        ];
    }
}
