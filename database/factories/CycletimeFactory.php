<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cycletime>
 */
class CycletimeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'activity_id' => Activity::factory(),
            'cycletime' => $this->faker->randomFloat(2, 1, 10), // between 1 to 10 with 2 decimal
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90),
            'created_by' => User::factory(),
        ];
    }
}