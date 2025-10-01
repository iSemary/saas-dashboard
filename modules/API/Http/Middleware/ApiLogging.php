<?php

namespace Modules\API\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiLogging
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
