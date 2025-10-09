<?php

namespace Database\Factories;

use App\Models\Cut;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Batch>
 */
class BatchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cut_id' => Cut::factory(),
            'quantity' => $this->faker->randomElement([300, 500]), // 300 or 500
            'printing_date' => $this->faker->dateTime(),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90),
            'created_by' => User::factory(),
        ];
    }
}