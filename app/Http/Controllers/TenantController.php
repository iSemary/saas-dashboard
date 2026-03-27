<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Tenant\Helper\TenantHelper;
use Modules\Tenant\Entities\Tenant;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $subdomain = TenantHelper::getSubDomain();

        if ($subdomain === 'landlord') {
            return app()->call('Modules\Auth\Http\Controllers\Landlord\DashboardController@index');
        }

        $tenant = Tenant::where("domain", $subdomain)->first();
        if ($subdomain !== 'www' && $subdomain !== '' && $tenant) {
            TenantHelper::makeCurrent($tenant->name);
            
            // If user is authenticated, redirect to Next.js dashboard
            // Check if request is coming from Next.js (has X-Requested-With header or is API)
            // Otherwise, check nginx proxy or direct port access
            if (auth()->check()) {
                // Check if this is an API request or already in Next.js
                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['redirect' => '/dashboard']);
                }
                
                // Check if nginx is proxying Next.js (check if /dashboard route exists in Laravel)
                // If not, assume Next.js is proxied
                $nginxProxyEnabled = env('NGINX_PROXY_ENABLED', true);
                
                if ($nginxProxyEnabled) {
                    // Nginx is proxying, so use same domain
                    return redirect('/dashboard');
                } else {
                    // Direct connection to Next.js dev server
                    $protocol = $request->getScheme();
                    $host = $request->getHost();
                    $frontendPort = env('FRONTEND_PORT', '3000');
                    return redirect("{$protocol}://{$host}:{$frontendPort}/dashboard");
                }
            }
            
            return view('welcome');
        }

        return redirect()->route('login');
    }
}
