<?php

namespace Database\Seeders;

use App\Models\Month;
use App\Models\User;
use Illuminate\Database\Seeder;

class MonthSeeder extends Seeder
{
    public function run(): void
    {
        // Check if users exist
        $users = User::all();
        
        if ($users->isEmpty()) {
            echo "\n⚠️  No users found. Skipping month seeding.\n";
            return;
        }
        
        // Create default months (Shahrivar 1403 to Shahrivar 1404)
        $defaultMonths = [
            ['name' => '140306', 'description' => 'Shahrivar 1403'],
            ['name' => '140307', 'description' => 'Mehr 1403'],
            ['name' => '140308', 'description' => 'Aban 1403'],
            ['name' => '140309', 'description' => 'Azar 1403'],
            ['name' => '140310', 'description' => 'Dey 1403'],
            ['name' => '140311', 'description' => 'Bahman 1403'],
            ['name' => '140312', 'description' => 'Esfand 1403'],
            ['name' => '140401', 'description' => 'Farvardin 1404'],
            ['name' => '140402', 'description' => 'Ordibehesht 1404'],
            ['name' => '140403', 'description' => 'Khordad 1404'],
            ['name' => '140404', 'description' => 'Tir 1404'],
            ['name' => '140405', 'description' => 'Mordad 1404'],
            ['name' => '140406', 'description' => 'Shahrivar 1404'],
        ];
        
        $createdCount = 0;
        $adminUser = $users->first();
        
        foreach ($defaultMonths as $monthData) {
            if (!Month::where('name', $monthData['name'])->exists()) {
                Month::factory()->create(array_merge($monthData, [
                    'status' => true,
                    'created_by' => $adminUser->id,
                ]));
                $createdCount++;
            }
        }
        
        echo "\n✅ Created {$createdCount} month records.\n";
    }
}