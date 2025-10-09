<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**************************
     * The name of the factory's corresponding model
     *************************/
    protected $model = \App\Models\User::class;

    /**************************
     * Define the model's default state
     *************************/
    public function definition(): array
    {
        // Create a Faker instance with Persian locale
        $faker = \Faker\Factory::create('fa_IR');

        return [
            'gender' => $this->faker->randomElement(['male', 'female']),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password
            'remember_token' => Str::random(10),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90),
            'created_by' => null, // will be set to admin in seeder
            'email_verified_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
