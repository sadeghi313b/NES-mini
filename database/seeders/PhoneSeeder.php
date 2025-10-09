<?php

namespace Database\Seeders;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Database\Seeder;

class PhoneSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users or create some
        $users = User::all();
        
        if ($users->isEmpty()) {
            echo "\n⚠️  No users found. Skipping phone seeding.\n";
            return;
        }
        
        // Create 2-3 phones for each user
        foreach ($users as $user) {
            Phone::factory()->count(rand(2, 3))->create([
                'user_id' => $user->id,
                'created_by' => $user->id,
            ]);
        }
    }
}