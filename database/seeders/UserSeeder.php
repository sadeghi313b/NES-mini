<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**************************
     * Run the database seeds
     *************************/
    public function run(): void
    {
        $admin = \App\Models\User::create([
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'email'      => 'admin@example.com',
            'password'   => bcrypt('123'), // admin password
            'gender'     => 'male',
            'status'     => true,
            'created_by' => null, // admin created by nobody
]);

        // Create 10 sample users
        User::factory()->count(50)->create(['created_by'=>$admin->id]);
    }
}
