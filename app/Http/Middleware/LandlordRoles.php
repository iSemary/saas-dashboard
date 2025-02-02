<?php

namespace App\Http\Middleware;

use App\Constants\Landlord\Resources;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LandlordRoles
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get all roles from Resources class
        $roles = collect(Resources::getRoles())
            ->pluck('name')
            ->implode('|');

        // Check if user has any of the roles
        if (!$request->user() || !$request->user()->hasRole(explode('|', $roles))) {
            abort(403, translate('user_does_not_have_the_right_permissions.'));
        }

        return $next($request);
    }
}
