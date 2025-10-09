<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // UserSeeder::class,
            // RoleSeeder::class
            // RoleUserSeeder::class,
            // PhoneSeeder::class,

            // DepartmentSeeder::class,
            // SubDepartmentSeeder::class,
            // EmployeeSeeder::class,
            
            // MoldSeeder::class,
            // ApplicatorSeeder::class,
            // CustomerSeeder::class,
            // ProductSeeder::class,

            // MonthSeeder::class,
            // OrderSeeder::class,
            // DeadlineSeeder::class,
            // DeliverySeeder::class,
            // DeliveryOrderSeeder::class,

            // CutSeeder::class,
            // BatchSeeder::class,

            // ActivitySeeder::class,
            // CycletimeSeeder::class,
            TimesheetSeeder::class,
            EffeciencySeeder::class,
        ]);
    }
}