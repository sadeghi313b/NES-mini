<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Batch;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Timesheet>
 */
class TimesheetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'batch_id' => Batch::factory(),
            'activity_id' => Activity::factory(),
            'quantity' => $this->faker->numberBetween(100, 1000),
            'date' => $this->faker->date(),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90),
            'created_by' => User::factory(),
        ];
    }
}