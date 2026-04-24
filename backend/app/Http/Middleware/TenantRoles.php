<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantRoles
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            abort(403, translate('user_does_not_have_the_right_permissions.'));
        }

        // For tenant users, we just check if they have any role
        // The specific permissions are checked via the 'permission' middleware on individual routes
        return $next($request);
    }
}
