<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubDepartment>
 */
class SubDepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => $this->faker->word() . ' Unit',
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90),
            'created_by' => User::factory(),
        ];
    }
}