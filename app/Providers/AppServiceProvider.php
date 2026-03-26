<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('is-admin', fn ($user) => $user->role === 'admin');
        Gate::define('is-fan', fn ($user) => $user->role === 'fan');
    }
}
