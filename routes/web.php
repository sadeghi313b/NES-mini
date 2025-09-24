<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;






Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

/* -------------------------------------------------------------------------- */
/*                                    User                                    */
/* -------------------------------------------------------------------------- */

//static routs
Route::delete('users/bulk-destroy',[UserController::class, 'bulkDestroy'])->name('users.bulkDestroy');

// dynamic routes
Route::resource('users', UserController::class); //->except(['show']);
Route::resource('orders',OrderController::class);

// Additional  route
Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');


Route::fallback(function () {
    Log::info('Fallback hit: ' . request()->method() . ' ' . request()->path());
    return response()->json(['error' => 'web.php>route::fallback'], 404);
});

/* -------------------------------------------------------------------------- */
/*                                   Product                                  */
/* -------------------------------------------------------------------------- */
Route::resource('products', ProductController::class);
