<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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

// Resource routes for Users using Inertia
Route::resource('users', UserController::class);
    //->except(['show']); //  یه مسیر برای صفحه جزئیات یوزر نمی خواهیم

// Optional custom routes for user activation/deactivation
Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');


/* -------------------------------------------------------------------------- */
/*                                   Product                                  */
/* -------------------------------------------------------------------------- */
Route::resource('products', ProductController::class);
