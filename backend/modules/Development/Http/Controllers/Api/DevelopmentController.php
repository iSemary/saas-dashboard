<?php

namespace Modules\Development\Http\Controllers\Api;

use App\Http\Controllers\ApiController;

class DevelopmentController extends ApiController
{
    public function routes()
    {
        $routes = [
            ['name' => 'flows.modules', 'route' => route('landlord.development.flows.modules')],
            ['name' => 'flows.database', 'route' => route('landlord.development.flows.database')]
        ];
        return $this->return(200, 'Routes fetched successfully', ['routes' => $routes]);
    }
}
