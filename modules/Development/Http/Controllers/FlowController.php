<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;

class FlowController extends ApiController
{
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
