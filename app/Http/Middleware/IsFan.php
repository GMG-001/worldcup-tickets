<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class IsFan
{
    public function handle(Request $request, Closure $next): Response
    {
        Gate::authorize('is-fan');

        return $next($request);
    }
}
