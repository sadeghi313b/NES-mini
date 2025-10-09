<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryOrder;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $products = Product::all();
        $orders = Order::all();
        $users = User::all();
        
        if ($products->isEmpty() || $orders->isEmpty() || $users->isEmpty()) {
            echo "\n⚠️  Required data (products/orders/users) not found. Skipping delivery seeding.\n";
            return;
        }
        
        // Create deliveries
        $createdCount = 0;
        $deliveryOrderCount = 0;
        
        // Create 2-3 deliveries for random products
        $deliveryCount = rand(2, 3);
        for ($i = 0; $i < $deliveryCount; $i++) {
            $delivery = Delivery::factory()->create([
                'product_id' => $products->random()->id,
                'created_by' => $users->random()->id,
            ]);
            $createdCount++;
            
            // Create delivery orders for this delivery
            $orderCount = rand(1, 2);
            $relatedOrders = $orders->random(min($orderCount, $orders->count()));
            
            foreach ($relatedOrders as $order) {
                DeliveryOrder::factory()->create([
                    'delivery_id' => $delivery->id,
                    'order_id' => $order->id,
                    'created_by' => $users->random()->id,
                ]);
                $deliveryOrderCount++;
            }
        }
        
        echo "\n✅ Created {$createdCount} delivery records and {$deliveryOrderCount} delivery order records.\n";
    }
}