<?php

namespace Database\Seeders;

use App\Models\Cycletime;
use App\Models\Product;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;

class CycletimeSeeder extends Seeder
{
    public function run(): void
    {
        // Check if required data exists
        $products = Product::all();
        $activities = Activity::all();
        $users = User::all();
        
        if ($products->isEmpty() || $activities->isEmpty() || $users->isEmpty()) {
            echo "\n⚠️  Required data (products/activities/users) not found. Skipping cycletime seeding.\n";
            return;
        }
        
        // Create cycletimes
        $createdCount = 0;
        
        // Create 1-2 cycletimes for each product-activity combination
        foreach ($products as $product) {
            $relatedActivities = $activities->random(min(2, $activities->count()));
            //  $activities->random(2) ≡ collection of to random models
            
            foreach ($relatedActivities as $activity) {
                if (!Cycletime::where('product_id', $product->id)
                              ->where('activity_id', $activity->id)
                              ->exists()) //no duplicated productId+activityId
                {
                    Cycletime::factory()->create([
                        'product_id' => $product->id,
                        'activity_id' => $activity->id,
                        'created_by' => $users->random()->id,
                    ]);
                    $createdCount++;
                }
            }
        }
        
        echo "\n✅ Created {$createdCount} cycletime records.\n";
    }
}