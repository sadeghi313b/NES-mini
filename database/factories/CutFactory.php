<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cut>
 */
class CutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'quantity' => $this->faker->numberBetween(1000, 3000),
            'maximum_batch_size' => $this->faker->randomElement([300, 500]),
            'printing_date' => $this->faker->dateTime(),
            'cutting_date' => $this->faker->date(),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90),
            'created_by' => User::factory(),
        ];
    }
}