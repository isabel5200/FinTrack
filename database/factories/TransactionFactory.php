<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 20),
            'amount' => fake()->numberBetween(1, 1000),
            'type' => fake()->randomElement(['income', 'expense']),
            'category_id' => fake()->numberBetween(1, 20),
            'description' => fake()->sentence(),
            'attachment' => fake()->imageUrl(),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
