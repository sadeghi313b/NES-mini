<?php

namespace Database\Seeders;

use App\Models\Mold;
use Illuminate\Database\Seeder;

class MoldSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 sample molds
        Mold::factory()->count(25)->create();
    }
}