<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Cut;
use App\Models\User;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $cuts = Cut::all();
        $users = User::all();
        
        if ($cuts->isEmpty() || $users->isEmpty()) {
            echo "\n⚠️  Required data (cuts/users) not found. Skipping batch seeding.\n";
            return;
        }
        
        // Create batches
        $createdCount = 0;
        
        // Create 2-3 batches for each cut
        foreach ($cuts as $cut) {
            $count = rand(2, 3);
            Batch::factory()->count($count)->create([
                'cut_id' => $cut->id,
                'created_by' => $users->random()->id,
            ]);
            $createdCount += $count;
        }
        
        echo "\n✅ Created {$createdCount} batch records.\n";
    }
}