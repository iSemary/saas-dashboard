<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;

class OrganizationController extends ApiController
{
    public function check(Request $request)
    {
        // Add logic to check organization
        $tenant = Tenant::where("name", $request->organization_name)->exists();
        if ($tenant) {
            return $this->return(200, "Organization checked successfully.");
        }
        return $this->return(400, "Organization not exists.");
    }
}
