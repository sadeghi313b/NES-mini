<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Applicator;
use App\Models\Mold;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'customer_id' => Customer::factory(),
            'cable_name' => $this->faker->randomElement(['3x0.75', '3x1', '2x1', '2x76']),
            'cable_length' => $this->faker->numberBetween(0, 300),
            'cable_color' => $this->faker->randomElement(['gray', 'white', 'black']),
            'cable_tall_strip_length' => $this->faker->numberBetween(0, 50),
            'cable_short_strip_length' => $this->faker->numberBetween(0, 10),
            'blue_wire_cut_length' => $this->faker->numberBetween(0, 10),
            'brown_wire_cut_length' => $this->faker->numberBetween(0, 10),
            'yellow_wire_cut_length' => $this->faker->numberBetween(0, 10),
            'blue_wire_strip_length' => $this->faker->numberBetween(0, 6),
            'brown_wire_strip_length' => $this->faker->numberBetween(0, 6),
            'yellow_wire_strip_length' => $this->faker->numberBetween(0, 6),
            'blue_wire_applicator_id' => Applicator::factory(),
            'brown_wire_applicator_id' => Applicator::factory(),
            'yellow_wire_applicator_id' => Applicator::factory(),
            'molds_id' => Mold::factory(),
            'cord_length' => $this->faker->numberBetween(5, 20),
            'double_wire_applicator_id' => Applicator::factory(),
            'double_wire_length' => $this->faker->numberBetween(15, 30),
            'plug_type' => $this->faker->randomElement(['Ref', 'Tv', 'Triple']),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90), // 90% true
            'created_by' => User::factory(),
        ];
    }
}