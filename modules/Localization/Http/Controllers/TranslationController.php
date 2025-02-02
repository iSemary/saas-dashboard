<?php

namespace Modules\Localization\Http\Controllers;

use App\Helpers\CryptHelper;
use App\Http\Controllers\ApiController;
use Exception;
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
                'icon' => '<i class="fas fa-plus"></i>',
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.translations.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
            [
                'text' => translate("generate_translations_json"),
                'icon' => '<i class="far fa-file"></i>',
                'class' => 'btn-sm btn-orange text-white generate-translations-json',
                'attr' => [
                    'data-route' => route('landlord.translations.generate-json'),
                    'data-method' => 'POST',
                ]
            ],
            [
                'text' => translate("sync_missing_translations"),
                'icon' => '<i class="fas fa-sync"></i>',
                'class' => 'btn-sm btn-orange text-white sync-missing-translations',
                'attr' => [
                    'data-route' => route('landlord.translations.sync-missing'),
                    'data-method' => 'POST',
                ]
            ],
            [
                'text' => translate("scan_translation_keys_in_js_files"),
                'icon' => '<i class="fas fa-search"></i>',
                'class' => 'open-details-btn btn-sm btn-info',
                'attr' => [
                    'data-modal-link' => route('landlord.translations.used-translation-js'),
                    'data-modal-title' => translate("scan_translation_keys_in_js_files"),
                ]
            ],
            [
                'text' => translate("scan_translation_keys_in_php_files"),
                'icon' => '<i class="fas fa-search"></i>',
                'class' => 'open-details-btn btn-sm btn-info',
                'attr' => [
                    'data-modal-link' => route('landlord.translations.used-translation-php'),
                    'data-modal-title' => translate("scan_translation_keys_in_php_files"),
                ]
            ],
            [
                'text' => translate("translations_status"),
                'icon' => '<i class="fas fa-search"></i>',
                'class' => 'btn-sm btn-info',
                'redirect' => route('landlord.translations.showStatus')
            ],
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

    public function getObjectTranslations(Request $request, int $objectId)
    {
        // $request->object_type is an encrypted class like [Modules\Utilities\Entities\Category]
        $objectType = $request->object_type;
        $decryptedObjectType = CryptHelper::decrypt($objectType);

        // $request->object_key is an encrypted column like [name]
        $objectKey = $request->object_key;
        $decryptedObjectKey = CryptHelper::decrypt($objectKey);

        $languages = $this->languageService->getAll();

        $row = $decryptedObjectType::select(['id', $decryptedObjectKey])->whereId($objectId)->first();

        return view("user.localizations.object-manager", [
            'languages' => $languages,
            'objectType' => $objectType,
            'objectKey' => $objectKey,
            'row' => $row,
            'key' => $decryptedObjectKey
        ]);
    }

    public function updateObjectTranslations(Request $request, int $objectId)
    {
        try {
            // $request->object_type is an encrypted class like [Modules\Utilities\Entities\Category]
            $objectType = $request->object_type;
            $decryptedObjectType = CryptHelper::decrypt($objectType);

            if (!class_exists($decryptedObjectType)) {
                return $this->return(400, translate("invalid_class"));
            }

            // $request->object_key is an encrypted column like [name]
            $objectKey = $request->object_key;
            $decryptedObjectKey = CryptHelper::decrypt($objectKey);

            if (!property_exists($decryptedObjectType, 'fillable')) {
                return $this->return(400, translate("missing_fillable_property"));
            }

            // Process the translations [this will contain an array of locale as keys and translation values as the value]
            $translations = $request->{$decryptedObjectKey};

            if (!count($translations) && !is_string($decryptedObjectKey) && !is_string($decryptedObjectType)) {
                return $this->return(400, translate("something_went_wrong"));
            }

            // Update translations using the service
            $this->service->updateObjectTranslations($decryptedObjectType, $decryptedObjectKey, $objectId, $translations);

            return $this->return(200, translate("translations_updated_successfully"));
        } catch (Exception $e) {
            return $this->return(400, translate("something_went_wrong"), debug: $e->getMessage());
        }
    }

    public function getUsedTranslationInJs()
    {
        $keys = $this->service->getUsedTranslationInJs();
        return view('landlord.localizations.translations.used-translations', ['keys' => $keys]);
    }

    public function getUsedTranslationInPhp()
    {
        $keys = $this->service->getUsedTranslationInPhp();        
        return view('landlord.localizations.translations.used-translations', ['keys' => $keys]);
    }

    public function showStatus()
    {
        $languages = $this->languageService->getLanguagesStatus();
        return view('landlord.localizations.translations.status', ['languages' => $languages]);
    }
}
