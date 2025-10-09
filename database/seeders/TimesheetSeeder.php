<?php

namespace Database\Seeders;

use App\Models\Timesheet;
use App\Models\Employee;
use App\Models\Batch;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;

class TimesheetSeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $employees = Employee::all();
        $batches = Batch::all();
        $activities = Activity::all();
        $users = User::all();
        
        if ($employees->isEmpty() || $batches->isEmpty() || $activities->isEmpty() || $users->isEmpty()) {
            echo "\n⚠️  Required data (employees/batches/activities/users) not found. Skipping timesheet seeding.\n";
            return;
        }
        
        // Create timesheets
        $createdCount = 0;
        
        // Create 2-3 timesheets for each employee-batch-activity combination
        foreach ($employees as $employee) {
            foreach ($batches->random(min(2, $batches->count())) as $batch) {
                foreach ($activities->random(min(2, $activities->count())) as $activity) {
                    // Check if this combination already exists
                    if (!Timesheet::where('employee_id', $employee->id)
                                  ->where('batch_id', $batch->id)
                                  ->where('activity_id', $activity->id)
                                  ->exists()) {
                        Timesheet::factory()->create([
                            'employee_id' => $employee->id,
                            'batch_id' => $batch->id,
                            'activity_id' => $activity->id,
                            'date' => now()->subDays(rand(0, 30)), // Random date in last 30 days
                            'created_by' => $users->random()->id,
                        ]);
                        $createdCount++;
                    }
                }
            }
        }
        
        echo "\n✅ Created {$createdCount} timesheet records.\n";
    }
}