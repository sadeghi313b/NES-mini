<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;








Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';


/* -------------------------------------------------------------------------- */
/*                                   tables                                   */
/* -------------------------------------------------------------------------- */
function getTableNamesFromMigrations()
{
    $migrations = DB::table('migrations')->pluck('migration')->toArray();

    $excludedTables = ['jobs', 'job_batches', 'failed_jobs'];

    $tableNames = [];
    foreach ($migrations as $migration) {
        if (preg_match('/create_(\w+)_table/', $migration, $matches)) {
            $tableName = $matches[1];
            if (!in_array($tableName, $excludedTables)) {
                $tableNames[] = $tableName;
            }
        }
    }

    return $tableNames;
}

$tables = getTableNamesFromMigrations();




/* -------------------------------------------------------------------------- */
/*                                static routs                                */
/* -------------------------------------------------------------------------- */

//temp Route::delete('dashboard/orders/bulk-destroy', [OrderController::class, 'bulkDestroy'])->name('dashboard.orders.bulkDestroy');
foreach ($tables as $table) {
    Route::delete("dashboard/{$table}/bulk-destroy", function () use ($table) {
        $controllerName = ucfirst($table) . 'Controller';
        $controller = app("App\\Http\\Controllers\\$controllerName");
        return $controller->bulkDestroy(request());
    })->name("dashboard.{$table}.bulk-destroy");
}

Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');




/* -------------------------------------------------------------------------- */
/*                                 controllers                                */
/* -------------------------------------------------------------------------- */
use Illuminate\Support\Str;

$controllerFiles = scandir(app_path('Http/Controllers'));
$controllers = [];

foreach ($controllerFiles as $file) {
    if (str_ends_with($file, 'Controller.php')) {
        $controllerName = basename($file, 'Controller.php');
        if ($controllerName !== 'Controller') {
            $routeName = Str::snake(Str::plural(lcfirst($controllerName))); // مثلاً: UserManagement → user_managements
            $controllers[$routeName] = $controllerName . 'Controller';
        }
    }
}

Route::prefix('dashboard')->name('dashboard.')->group(function () use ($controllers) {
    foreach ($controllers as $routeName => $controllerClass) {
        if (class_exists("App\\Http\\Controllers\\$controllerClass")) {
            Route::resource($routeName, "App\\Http\\Controllers\\$controllerClass");
        }
    }
});


/* -------------------------------------------------------------------------- */
/*                                  fallback                                  */
/* -------------------------------------------------------------------------- */
Route::fallback(function () {
    return response()->json(['error' => 'web.php>route::fallback'], 404);
});

