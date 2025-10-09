<?php

namespace Database\Seeders;

use App\Models\Cut;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class CutSeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $orders = Order::all();
        $users = User::all();
        
        if ($orders->isEmpty() || $users->isEmpty()) {
            echo "\n⚠️  Required data (orders/users) not found. Skipping cut seeding.\n";
            return;
        }
        
        // Create cuts
        $createdCount = 0;
        
        // Create 1-2 cuts for each order
        foreach ($orders as $order) {
            $count = rand(1, 2);
            Cut::factory()->count($count)->create([
                'order_id' => $order->id,
                'created_by' => $users->random()->id,
            ]);
            $createdCount += $count;
        }
        
        echo "\n✅ Created {$createdCount} cut records.\n";
    }
}