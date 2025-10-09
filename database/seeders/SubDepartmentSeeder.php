<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\SubDepartment;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $departments = Department::all();
        $users = User::all();
        
        if ($departments->isEmpty() || $users->isEmpty()) {
            echo "\n⚠️  Required data (departments/users) not found. Skipping sub-department seeding.\n";
            return;
        }
        
        // Create sub-departments for each department
        $createdCount = 0;
        $adminUser = $users->first();
        
        foreach ($departments as $department) {
            // Create 2-3 sub-departments for each department
            $subDepts = [
                ['name' => $department->name . ' Line 1', 'description' => 'Production line 1'],
                ['name' => $department->name . ' Line 2', 'description' => 'Production line 2'],
                ['name' => $department->name . ' Support', 'description' => 'Support services'],
            ];
            
            foreach (array_slice($subDepts, 0, rand(2, 3)) as $subDeptData) {
                if (!SubDepartment::where('name', $subDeptData['name'])->exists()) {
                    try {
                        SubDepartment::factory()->create(array_merge($subDeptData, [
                            'department_id' => $department->id,
                            'status' => true,
                            'created_by' => $adminUser->id,
                        ]));
                        $createdCount++;
                    } catch (\Exception $e) {
                        // Skip if constraint fails
                        continue;
                    }
                }
            }
        }
        
        echo "\n✅ Created {$createdCount} sub-department records.\n";
    }
}