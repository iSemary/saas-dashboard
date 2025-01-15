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
            return view('landlord.dashboard');
        }

        $tenant = Tenant::where("domain", $subdomain)->first();
        if ($subdomain !== 'www' && $subdomain !== '' && $tenant) {
            TenantHelper::makeCurrent($tenant->name);
            return view('welcome');
        }

        return redirect()->route('login');
    }
}
