<?php

namespace Modules\Tenant\Helper;

use Modules\Auth\Entities\User;
use Modules\Tenant\Entities\Tenant;
use Illuminate\Http\Request;

class TenantHelper
{
    /**
     * Get the subdomain from the current request's host.
     *
     * This method retrieves the host from the current request and splits it into segments.
     * If the host contains more than two segments (indicatin$tenantExists->subdomaing the presence of a subdomain),
     * it returns the first segment as the subdomain. Otherwise, it returns null.
     *
     * @return string|null The subdomain if present, otherwise null.
     */
    public static function getSubDomain()
    {
        // Get the host (e.g., landlord.saas.test or saas.test)
        $host = request()->getHost();

        // Split the host into segments
        $segments = explode('.', $host);

        // Check if there's a subdomain (more than two segments for domain and TLD)
        return count($segments) > 2 ? $segments[0] : null;
    }

    /**
     * Formats the given customer username by performing the following operations:
     * - Trims whitespace from the beginning and end of the username.
     * - Removes all non-alphanumeric characters.
     * - Converts the username to lowercase.
     * - Removes all spaces from the username.
     *
     * @param string $customerUsername The customer username to be formatted.
     * @return string The formatted customer username.
     */
    public static function format($customerUsername)
    {
        $formattedCustomerUsername = trim($customerUsername);
        $formattedCustomerUsername = preg_replace('/[^a-zA-Z0-9]/', '', $formattedCustomerUsername);
        $formattedCustomerUsername = strtolower($formattedCustomerUsername);
        $formattedCustomerUsername = str_replace(" ", "", $formattedCustomerUsername);
        return $formattedCustomerUsername;
    }

    /**
     * Switches the database connection to the appropriate tenant or landlord database
     * based on the provided customer username and makes the tenant current.
     *
     * @param string $customerUsername The username of the customer to switch to.
     * @return Tenant The tenant instance that was made current.
     */
    public static function makeCurrent($customerUsername)
    {
        if ($customerUsername == env("APP_LANDLORD_ORGANIZATION_NAME")) {
            // Switch to the landlord database
            config(['database.default' => 'landlord']);
            $tenant = Tenant::on('landlord')->where('name', 'landlord')->first();
            User::on('landlord');
        } else {
            // Switch to the tenant database
            $tenant = Tenant::where('name', $customerUsername)->first();
            config(['database.default' => 'tenant']);
            config(['database.logs.database' => $tenant->database]);
            User::on('tenant');
        }

        if ($tenant) {
            $tenant->makeCurrent();
        }
        return $tenant;
    }

    /**
     * Generates a URL for a tenant based on the customer's username.
     *
     * This method constructs a URL using the protocol and domain settings
     * from the configuration and appends the customer's username as a subdomain.
     *
     * @param string $customerUsername The username of the customer.
     * @return string The generated URL.
     */
    public static function generateURL($customerUsername)
    {
        return config("settings.protocol") . "://" . $customerUsername . "." . config("settings.domain");
    }

    public static function handleRedirection(Request $request, $customSubDomain = null, $customRedirection = null, $withoutQuery = false)
    {
        $subDomain = $customSubDomain ?? self::getSubDomain();

        if (!$subDomain || (!$customSubDomain && !Tenant::where('domain', $subDomain)->exists())) {
            return '/';
        }

        $redirection = self::generateURL($subDomain) . self::getRedirectQueryString($request, $customRedirection);
        if ($withoutQuery) {
            $redirection = str_replace(['?redirect='], [''], $redirection);
        }

        return $redirection;
    }

    private static function getRedirectQueryString(Request $request, $customRedirection = null): string
    {
        if ($customRedirection) {
            return "?redirect=" . $customRedirection;
        }
        if ($request->redirect && !empty($request->redirect)) {
            return "?redirect=" . $request->redirect;
        }
        return "";
    }
}
