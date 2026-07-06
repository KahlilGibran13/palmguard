<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        //
        Gate::define('manage-by-admin', function (User $user) {
            return $user->role === 'admin';
        });
        Gate::define('manage-by-operator', function (User $user) {
            return $user->role === 'operator';
        });
        Gate::define('manage-by-manager', function (User $user) {
            return $user->role === 'manager';
        });
        
    }
}
