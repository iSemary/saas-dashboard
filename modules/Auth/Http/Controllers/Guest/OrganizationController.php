<?php

namespace Modules\Auth\Http\Controllers\Guest;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Helper\TenantHelper;

class OrganizationController extends ApiController
{
    public function check(Request $request)
    {
        if ($request->organization_name === env("APP_LANDLORD_ORGANIZATION_NAME")) {
            return $this->return(200, "Organization checked successfully.", ['redirect' => TenantHelper::handleRedirection($request, env("APP_LANDLORD_ORGANIZATION_NAME"), "/login")]);
        }

        $tenantExists = Tenant::where("name", $request->organization_name)->exists();
        if ($tenantExists) {
            return $this->return(200, "Organization checked successfully.", ['redirect' => TenantHelper::handleRedirection($request, $tenantExists->subdomain, "/login")]);
        }

        return $this->return(400, "Organization does not exist.");
    }
}
