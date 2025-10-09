<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deadline>
 */
class DeadlineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'part_quantity' => $this->faker->numberBetween(1000, 10000),
            'due_date' => $this->faker->date(),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90),
            'created_by' => User::factory(),
        ];
    }
}