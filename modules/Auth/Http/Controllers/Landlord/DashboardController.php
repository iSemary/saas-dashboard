<?php

namespace Modules\Auth\Http\Controllers\Landlord;

use App\Http\Controllers\ApiController;
use Modules\Localization\Services\LanguageService;

class DashboardController extends ApiController
{
    protected $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }
    public function index()
    {
        $languages = $this->languageService->getLanguagesStatus();

        return view('landlord.dashboard.index', [
            'languages' => $languages,
        ]);
    }
}
