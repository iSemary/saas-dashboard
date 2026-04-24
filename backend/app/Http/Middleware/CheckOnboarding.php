<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Customer\Entities\Brand;

class CheckOnboarding
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip for API routes, onboarding routes, and auth routes
        if ($request->is('api/*') || 
            $request->is('onboarding/*') || 
            $request->is('login') || 
            $request->is('register') || 
            $request->is('verify/*') ||
            !auth()->check()) {
            return $next($request);
        }

        // Check if user has completed onboarding
        $tenantId = session('tenant_id');
        if ($tenantId) {
            $completedBrands = Brand::where('tenant_id', $tenantId)
                ->whereNotNull('metadata->onboarding_completed')
                ->count();

            // If no brands with completed onboarding, redirect to onboarding
            if ($completedBrands === 0) {
                return redirect()->route('onboarding.welcome');
            }
        }

        return $next($request);
    }
}
