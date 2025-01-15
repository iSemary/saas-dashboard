<?php

namespace Modules\Auth\Http\Controllers\Guest;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Modules\Tenant\Entities\Tenant;

class OrganizationController extends ApiController
{
    public function check(Request $request)
    {
        if ($request->organization_name === env("APP_LANDLORD_ORGANIZATION_NAME")) {
            return $this->return(200, "Organization checked successfully.");
        }

        $tenantExists = Tenant::where("name", $request->organization_name)->exists();
        if ($tenantExists) {
            return $this->return(200, "Organization checked successfully.");
        }

        return $this->return(400, "Organization does not exist.");
    }
}
