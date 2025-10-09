<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Check if users exist
        $users = User::all();
        
        if ($users->isEmpty()) {
            echo "\n⚠️  No users found. Skipping department seeding.\n";
            return;
        }
        
        // Create default departments
        $defaultDepartments = [
            ['name' => 'Production', 'description' => 'Manufacturing and production operations'],
            ['name' => 'Quality Control', 'description' => 'Quality assurance and control'],
            ['name' => 'Maintenance', 'description' => 'Equipment maintenance and repair'],
            ['name' => 'Warehouse', 'description' => 'Storage and inventory management'],
            ['name' => 'Administration', 'description' => 'General administration and management'],
        ];
        
        $createdCount = 0;
        $adminUser = $users->first();
        
        foreach ($defaultDepartments as $deptData) {
            if (!Department::where('name', $deptData['name'])->exists()) {
                Department::factory()->create(array_merge($deptData, [
                    'status' => true,
                    'created_by' => $adminUser->id,
                ]));
                $createdCount++;
            }
        }
        
        echo "\n✅ Created {$createdCount} department records.\n";
    }
}