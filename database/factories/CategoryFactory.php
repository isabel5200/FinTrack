<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 20),
            'name' => fake()->randomElement(['Personal', 'Work', 'Family', 'Hobbies']),
            'type' => fake()->randomElement(['income', 'expense']),
        ];
    }
}
