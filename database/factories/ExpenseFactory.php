<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $faker = FakerFactory::create();

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'amount' => $faker->randomFloat(2, 5, 500),
            'description' => $faker->sentence(),
            'date' => $faker->date(),
        ];
    }
}
