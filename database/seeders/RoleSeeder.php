<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create default roles using factory
        $defaultRoles = [
            ['name' => 'Admin', 'description' => 'Administrator with full access'],
            ['name' => 'CEO', 'description' => 'مدیریت عامل'],
            ['name' => 'Injection_director', 'description' => 'تزریق'],
            ['name' => 'Injection_worker', 'description' => 'گارگر تزریق'],
            ['name' => 'IE_director', 'description' => 'مهندسی صنایع'],
            ['name' => 'QC_director', 'description' => 'گنترل گیفی'],
            ['name' => 'Sale_director', 'description' => 'فروش'],
            ['name' => 'Sale_director', 'description' => 'فروش'],
            ['name' => 'guest', 'description' => 'مهمان'],
        ];

        foreach ($defaultRoles as $roleData) {
            Role::factory()->create( array_merge(
                $roleData, 
                [
                    'status' => true,
                    'created_by' => 1,
                ]
            ));
        }
    }
}