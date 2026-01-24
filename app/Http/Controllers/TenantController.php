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
            // If nginx is configured to proxy, redirect to /dashboard
            // Otherwise, redirect to Next.js on port 3000
            if (auth()->check()) {
                if (env('NGINX_PROXY_ENABLED', false)) {
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
