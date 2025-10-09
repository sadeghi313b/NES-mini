<?php

namespace Database\Seeders;

use App\Models\Applicator;
use Illuminate\Database\Seeder;

class ApplicatorSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 sample applicators
        Applicator::factory()->count(15)->create();
    }
}