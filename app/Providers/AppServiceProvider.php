<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{


    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('Helpers/customHelpers.php');

        //Gate::authorize('master');//usage
        Gate::define('master', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('injection_director');
        });
        
    }
}
