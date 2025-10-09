<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // Check if users exist
        $users = User::all();
        
        if ($users->isEmpty()) {
            echo "\n⚠️  No users found. Skipping activity seeding.\n";
            return;
        }
        
        // Create default activities
        // $defaultActivities = [
        //     ['name' => 'Cutting', 'interchangable_category' => 'Production', 'description' => 'Cable cutting process'],
        //     ['name' => 'Stripping', 'interchangable_category' => 'Production', 'description' => 'Wire stripping process'],
        //     ['name' => 'Applying', 'interchangable_category' => 'Production', 'description' => 'Applicator application'],
        //     ['name' => 'Molding', 'interchangable_category' => 'Production', 'description' => 'Cable molding process'],
        //     ['name' => 'Quality Check', 'interchangable_category' => 'QC', 'description' => 'Quality control inspection'],
        //     ['name' => 'Packaging', 'interchangable_category' => 'Warehouse', 'description' => 'Product packaging'],
        // ];
        $defaultActivities = [
            ['name' => 'Cutting'   , 'description' => 'Cable cutting process'],
            ['name' => 'Stripping'   , 'description' => 'Wire stripping process'],
            ['name' => 'Applying'   , 'description' => 'Applicator application'],
            ['name' => 'Molding'   , 'description' => 'Cable molding process'],
            ['name' => 'Quality Check'   , 'description' => 'Quality control inspection'],
            ['name' => 'Packaging'   , 'description' => 'Product packaging'],
        ];
        
        $createdCount = 0;
        $adminUser = $users->first();
        
        foreach ($defaultActivities as $activityData) {
            if (!Activity::where('name', $activityData['name'])->exists()) {
                Activity::factory()->create(array_merge($activityData, [
                    'status' => true,
                    'created_by' => $adminUser->id,
                ]));
                $createdCount++;
            }
        }
        
        echo "\n✅ Created {$createdCount} activity records.\n";
    }
}