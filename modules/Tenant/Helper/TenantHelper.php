<?php

namespace Modules\Tenant\Helper;

use Modules\Auth\Entities\User;
use Modules\Tenant\Entities\Tenant;

class TenantHelper
{
    public static function getSubDomain()
    {
        // Get the host (e.g., landlord.sass.test or sass.test)
        $host = request()->getHost();
    
        // Split the host into segments
        $segments = explode('.', $host);
    
        // Check if there's a subdomain (more than two segments for domain and TLD)
        return count($segments) > 2 ? $segments[0] : null;
    }
    

    public static function format($customerUsername)
    {
        $formattedCustomerUsername = trim($customerUsername);
        $formattedCustomerUsername = preg_replace('/[^a-zA-Z0-9]/', '', $formattedCustomerUsername);
        $formattedCustomerUsername = strtolower($formattedCustomerUsername);
        $formattedCustomerUsername = str_replace(" ", "", $formattedCustomerUsername);
        return $formattedCustomerUsername;
    }

    public static function makeCurrent($customerUsername)
    {
        if ($customerUsername == env("APP_LANDLORD_ORGANIZATION_NAME")) {
            // Switch to the landlord database
            config(['database.default' => 'landlord']);
            $tenant = Tenant::on('landlord')->where('name', $customerUsername)->first();
            User::on('landlord');
        } else {
            // Switch to the tenant database
            $tenant = Tenant::where('name', $customerUsername)->first();
            config(['database.default' => 'tenant']);
            config(['database.logs.database' => $tenant->database]);
            User::on('tenant');
        }

        $tenant->makeCurrent();
        return $tenant;
    }

    public static function generateURL($customerUsername)
    {
        return config("settings.protocol") . "://" . $customerUsername . "." . config("settings.domain");
    }
}
