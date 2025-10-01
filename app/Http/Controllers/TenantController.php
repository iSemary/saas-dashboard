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
            // Ensure landlord connection is set
            config(['database.default' => 'landlord']);
            return app()->call('Modules\Auth\Http\Controllers\Landlord\DashboardController@index');
        }

        $tenant = Tenant::where("domain", $subdomain)->first();
        if ($subdomain !== 'www' && $subdomain !== '' && $tenant) {
            TenantHelper::makeCurrent($tenant->name);
            return app()->call('Modules\Auth\Http\Controllers\Tenant\DashboardController@index');
        }

        return redirect()->route('login');
    }
}
