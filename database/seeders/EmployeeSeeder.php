<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\SubDepartment;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $users = User::all();
        $departments = Department::all();
        $subDepartments = SubDepartment::all();
        
        if ($users->isEmpty() || $departments->isEmpty() || $subDepartments->isEmpty()) {
            echo "\n⚠️  Required data (users/departments/sub_departments) not found. Skipping employee seeding.\n";
            return;
        }
        
        // Create employees for some users (not all users are employees)
        $employeeUsers = $users->random(min(5, $users->count()));
        $createdCount = 0;
        
        foreach ($employeeUsers as $user) {
            // Skip if user already has an employee record
            if (Employee::where('user_id', $user->id)->exists()) {
                continue;
            }
            
            try {
                Employee::factory()->create([
                    'user_id' => $user->id,
                    'department_id' => $departments->random()->id,
                    'sub_department_id' => $subDepartments->random()->id,
                    'created_by' => $users->random()->id,
                ]);
                $createdCount++;
            } catch (\Exception $e) {
                // Skip if foreign key constraint fails
                continue;
            }
        }
        
        echo "\n✅ Created {$createdCount} employee records.\n";
    }
}