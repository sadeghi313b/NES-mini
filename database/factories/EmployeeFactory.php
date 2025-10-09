<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Department;
use App\Models\SubDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'sub_department_id' => SubDepartment::factory(),
            'employee_number' => $this->faker->unique()->numberBetween(1000, 5000),
            'hire_date' => $this->faker->date(),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90),
            'created_by' => User::factory(),
        ];
    }
}