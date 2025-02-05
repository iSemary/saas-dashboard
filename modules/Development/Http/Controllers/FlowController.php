<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class FlowController extends ApiController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.modules_flow', only: ['modules']),
            new Middleware('permission:read.database_flow', only: ['database']),
        ];
    }

    public function modules()
    {
        $title = translate("flows");

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate("flows")],
            ['text' => translate("modules")],
        ];

        return view('landlord.developments.flows.modules', ['title' => $title, 'breadcrumbs' => $breadcrumbs]);
    }

    public function database()
    {
        $title = translate("flows");

        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate("flows")],
            ['text' => translate("database")],
        ];

        return view('landlord.developments.flows.database', ['title' => $title, 'breadcrumbs' => $breadcrumbs]);
    }
}
