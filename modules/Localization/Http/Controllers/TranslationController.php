<?php

namespace Modules\Localization\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Localization\Services\TranslationService;
use Illuminate\Http\Request;
use Modules\Localization\Services\LanguageService;

class TranslationController extends ApiController
{
    protected $service;
    protected $languageService;

    public function __construct(TranslationService $service, LanguageService $languageService)
    {
        $this->service = $service;
        $this->languageService = $languageService;
    }
    public function index()
    {
        if (request()->ajax()) {
            return $this->service->getDataTables();
        }
        $title = translate($this->service->model->pluralTitle);
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.translations.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
            [
                'text' => translate("generate_translations_json"),
                'class' => 'btn-sm btn-orange text-white generate-translations-json',
                'attr' => [
                    'data-route' => route('landlord.translations.generate-json'),
                    'data-method' => 'POST',
                ]
            ],
            [
                'text' => translate("sync_missing_translations"),
                'class' => 'btn-sm btn-orange text-white sync-missing-translations',
                'attr' => [
                    'data-route' => route('landlord.translations.sync-missing'),
                    'data-method' => 'POST',
                ]
            ]
        ];

        return view('landlord.localizations.translations.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        $languages = $this->languageService->getAll();
        return view('landlord.localizations.translations.editor', compact('languages'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->service->create($data);
        return $this->return(200, translate("created_successfully"));
    }

    public function show($id) {}

    public function edit($id)
    {
        $row = $this->service->get($id);
        $languages = $this->languageService->getAll();
        return view('landlord.localizations.translations.editor', compact('row', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return $this->return(200, translate("updated_successfully"));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->return(200, "Deleted successfully");
    }

    public function restore($id)
    {
        $this->service->restore($id);
        return $this->return(200, "Deleted successfully");
    }

    public function generateJson()
    {
        $response = $this->service->generateJson();
        if ($response['success']) {
            return $this->return(200, "Generated successfully");
        }
        return $this->return(400, "Failed on generating json", ['errors' => $response['message']]);
    }

    public function syncMissing()
    {
        $this->service->syncMissing();
        return $this->return(200, "Synced successfully");
    }
}
