<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Month;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $products = Product::all();
        $months = Month::all();
        $users = User::all();
        
        if ($products->isEmpty() || $months->isEmpty() || $users->isEmpty()) {
            echo "\n⚠️  Required data (products/months/users) not found. Skipping order seeding.\n";
            return;
        }
        
        // Create 2-3 orders for each product
        $createdCount = 0;
        
        foreach ($products as $product) {
            $count = rand(2, 3);
            Order::factory()->count($count)->create([
                'product_id' => $product->id,
                'month_id' => $months->random()->id,
                'created_by' => $users->random()->id,
            ]);
            $createdCount += $count;
        }
        
        echo "\n✅ Created {$createdCount} order records.\n";
    }
}