<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Phone>
 */
class PhoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'pre_country_number' => '+98',
            'pre_zone_number' => $this->faker->numberBetween(100,900),
            'number' => $this->faker->regexify('9[0-9]{9}'), // 10 digits starting with 9
            'phone_type' => $this->faker->randomElement(['mobile', 'home', 'work|office', 'fax']),
            'is_main_for_sms' => $this->faker->boolean(30), // 30% chance
            'is_main_for_eitaa' => $this->faker->boolean(20), // 20% chance
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90), // 90% active
            'created_by' => User::factory(),
        ];
    }
}