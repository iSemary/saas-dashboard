<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Tenant\Helper\TenantHelper;

class SetDatabaseConnection
{
    public function handle($request, Closure $next)
    {
        $subdomain = TenantHelper::getSubDomain();

        if ($subdomain === 'landlord') {
            // Use landlord connection for landlord subdomain
            Config::set('database.default', 'landlord');
            DB::purge('landlord');
        } else if ($subdomain) {
            // For other subdomains, set up tenant connection
            $tenant = \Modules\Tenant\Entities\Tenant::on('landlord')->where('domain', $subdomain)->first();
            if ($tenant) {
                Config::set('database.connections.tenant.database', $tenant->database);
                DB::purge('tenant');
                Config::set('database.default', 'tenant');
            } else {
                // Tenant not found, use landlord connection
                Config::set('database.default', 'landlord');
            }
        } else {
            // No subdomain, use default connection
            Config::set('database.default', 'landlord');
        }

        return $next($request);
    }
}
