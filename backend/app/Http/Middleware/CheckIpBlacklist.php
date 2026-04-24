<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Development\Entities\IpBlacklist;

class CheckIpBlacklist
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip(); // Get the client's IP address

        // Check if the IP exists in the blacklist
        $blacklisted = IpBlacklist::where('ip_address', $ip)->exists();

        if ($blacklisted) {
            abort(403, translate('Your IP is blacklisted.'));
        }

        return $next($request);
    }
}
