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
            Config::set('database.default', 'landlord');
            DB::purge('landlord');
        } else {
            Config::set('database.connections.tenant.database', $subdomain);
            DB::purge('tenant');
            Config::set('database.default', 'tenant');
        }

        return $next($request);
    }
}
