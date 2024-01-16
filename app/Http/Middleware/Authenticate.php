<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): array|string|null
    {
//        dd($request->expectsJson() || $request->acceptsJson());
        return ($request->expectsJson() || $request->acceptsJson()) ? ['message' => 'Unauthenticated'] : route('login');
    }
}
