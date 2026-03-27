<?php

namespace App\Providers;

use App\Enums\Role;
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
        Gate::define('is-admin', fn ($user) => $user->getRole() === Role::Admin);
        Gate::define('is-fan', fn ($user) => $user->getRole() === Role::Fan);
    }
}
