<?php
// php artisan my-migrator
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Migrates extends Command
{
    protected $signature = 'my-migrator';

    protected $description = 'Reset or migrate a list of migrations based on table existence';

    public function handle()
    {
        $migrations = [
            //  >>dir /b
            '0001_01_01_000001_create_cache_table.php',
            '0001_01_01_000001_create_cache_table.php',
            // '0001_01_01_000001_create_cache_table.php',
            // '0001_01_01_000002_create_jobs_table.php',
            
            // '2025_09_01_145725_create_users_table.php',
            // '2025_09_01_145725_create_roles_table.php',
            // '2025_09_01_145725_create_role_users_table.php',
            // '2025_09_01_145725_create_phones_table.php',
            
            // '2025_09_01_145727_create_departments_table.php',
            // '2025_09_01_145728_create_sub_departments_table.php',
            // '2025_09_01_145727_create_employees_table.php',
            
            // '2025_09_01_145725_create_molds_table.php',
            // '2025_09_01_145725_create_applicators_table.php',
            // '2025_09_01_145725_create_customers_table.php',
            // '2025_09_01_145725_create_products_table.php',
            
            //with controller and request
            // '2025_09_01_145726_create_months_table.php',
            // '2025_09_01_145726_create_orders_table.php',
            // '2025_09_01_145726_create_deadlines_table.php',
            // '2025_09_01_145727_create_deliveries_table.php',
            
            // '2025_09_01_145727_create_cuts_table.php',
            // '2025_09_01_145727_create_batches_table.php',
            
            // '2025_09_01_145728_create_activities_table.php',
            // '2025_09_01_145728_create_cycletimes_table.php',
            // '2025_09_01_145728_create_timesheets_table.php',
            // '2025_09_01_145729_create_effeciencies_table.php',
        ];

        $migrationPath = database_path('migrations');

        foreach ($migrations as $file) {
            $filePath = $migrationPath . '/' . $file;

            if (!file_exists($filePath)) {
                $this->warn("Migration file not found: $file");
                continue;
            }

            // Extract table name from file name
            if (preg_match('/create_(.+)_table\.php$/', $file, $matches)) {
                $table = $matches[1];

                // Check if table exists
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $this->info("Resetting migration for table: $table");
                    Artisan::call('migrate:reset', [
                        '--path' => 'database/migrations/' . $file,
                    ]);
                    $this->info(Artisan::output());
                } else {
                    $this->info("Table $table does not exist. Running migrate for it.");
                    Artisan::call('migrate', [
                        '--path' => 'database/migrations/' . $file,
                    ]);
                    $this->info(Artisan::output());
                }
            } else {
                $this->warn("Cannot determine table name from migration: $file");
            }
        }

        $this->info('Done processing migrations.');
    }
}
