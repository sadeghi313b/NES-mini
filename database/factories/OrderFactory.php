<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Month;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'month_id' => Month::factory(),
            'quantity' => $this->faker->numberBetween(2000, 80000),
            'notification_date' => $this->faker->date(),
            'seen' => $this->faker->boolean(30), // 30% seen
            'status' => $this->faker->randomElement(['active', 'force', 'hold', 'canceled', 'enough']),
            'description' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}