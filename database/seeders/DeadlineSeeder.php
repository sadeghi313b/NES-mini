<?php

namespace Database\Seeders;

use App\Models\Deadline;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeadlineSeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $orders = Order::all();
        $users = User::all();
        
        if ($orders->isEmpty() || $users->isEmpty()) {
            echo "\n⚠️  Required data (orders/users) not found. Skipping deadline seeding.\n";
            return;
        }
        
        // Create 1-2 deadlines for each order
        $createdCount = 0;
        
        foreach ($orders as $order) {
            $count = rand(1, 2);
            Deadline::factory()->count($count)->create([
                'order_id' => $order->id,
                'created_by' => $users->random()->id,
            ]);
            $createdCount += $count;
        }
        
        echo "\n✅ Created {$createdCount} deadline records.\n";
    }
}