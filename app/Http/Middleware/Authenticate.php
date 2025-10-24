<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // For API requests, don't redirect
        if ($request->expectsJson()) {
            return null;
        }

        // Check if the route exists, otherwise return null to prevent errors
        if (\Illuminate\Support\Facades\Route::has('crm.login')) {
            return route('crm.login');
        }

        return '/login';
    }
}
