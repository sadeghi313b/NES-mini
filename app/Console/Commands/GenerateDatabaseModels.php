<?php
// php artisan make:database-models
namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDatabaseModels extends Command
{
    /**************************
     * The name and signature of the console command.
     *************************/
    protected $signature = 'make:database-models';

    /**************************
     * The console command description.
     *************************/
    protected $description = 'Generate all models with migration, factory, seeder, resource controller, and form request';

    /**************************
     * List of all models to generate.
     *************************/
    protected $models = [
        'User',
        'Role',
        'RoleUser',
        'Phone',
        'Customer',
        'Product',
        'Applicator',
        'Mold',
        'Order',
        'Month',
        'Deadline',
        'DeliveryOrder',
        'Delivery',
        'Cut',
        'Batch',
        'Employee',
        'Department',
        'SubDepartment',
        'Activity',
        'Cycletime',
        'Timesheet',
        'Effeciency',
    ];

    /**************************
     * Execute the console command.
     *************************/
    public function handle()
    {
        foreach ($this->models as $model) {
            $this->info("Generating: $model");

            // Create model with migration
            $this->call('make:model', ['name' => $model, '-m' => true]);

            // Create factory
            $factoryName = $model . 'Factory';
            $this->call('make:factory', ['name' => $factoryName, '--model' => $model]);

            // Create seeder
            $seederName = $model . 'Seeder';
            $this->call('make:seeder', ['name' => $seederName]);

            // Create resource controller
            $controllerName = $model . 'Controller';
            $this->call('make:controller', ['name' => $controllerName, '--resource' => true]);

            // Create form request
            $requestName = $model . 'Request';
            $this->call('make:request', ['name' => $requestName]);
        }

        $this->info('All models, migrations, factories, seeders, controllers, and requests have been generated successfully!');
    }
}
