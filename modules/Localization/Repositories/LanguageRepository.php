<?php

namespace Modules\Localization\Repositories;

use App\Helpers\TableHelper;
use Modules\Localization\Entities\Language;
use Modules\Localization\Services\TranslationService;
use Yajra\DataTables\DataTables;

class LanguageRepository implements LanguageInterface
{
    protected $model;
    protected $translationService;

    public function __construct(Language $language, TranslationService $translationService)
    {
        $this->model = $language;
        $this->translationService = $translationService;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()
            ->withCount('translations')
            ->where(function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            });

        $totalEnglishTranslations = $this->model->where('locale', 'en')->withCount('translations')->first()->translations_count;

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.languages.edit',
                    deleteRoute: 'landlord.languages.destroy',
                    restoreRoute: 'landlord.languages.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: false,
                    showActivityLogs: $this->model
                );
            })
            ->addColumn('total_translations', function ($row) use ($totalEnglishTranslations) {
                return '<span class="' . ($totalEnglishTranslations == $row->translations_count ? 'text-success' : 'text-danger') . '">' . $row->translations_count . '</span>';
            })
            ->rawColumns(['actions', 'total_translations'])
            ->make(true);;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);
            return $row;
        }
        return null;
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->delete();
            return true;
        }
        return false;
    }

    public function restore($id)
    {
        $row = $this->model->withTrashed()->find($id);
        if ($row) {
            $row->restore();
            return true;
        }
        return false;
    }

    /**
     * Get the status of all languages.
     *
     * This method retrieves all languages along with their translation counts and calculates
     * the maximum counts for translations, shareable translations, JSON translations, and
     * datatable translations. It then determines the status of each language based on these
     * maximum counts.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of languages with their statuses.
     */
    public function getLanguagesStatus()
    {
        $languages = $this->model->withCount('translations')
            ->withCount(['translations as shareable_translations_count' => function ($query) {
                $query->where('translations.is_shareable', 1);
            }])
            ->get();

        // Add additional counts for translation objects through the translations relationship
        foreach ($languages as $language) {
            $language->total_without_objects = $language->translations->filter(function ($translation) {
                return $translation->translationObjects->isEmpty(); // No related translation objects
            })->count();

            $language->total_with_objects = $language->translations->filter(function ($translation) {
                return $translation->translationObjects->isNotEmpty(); // There are related translation objects
            })->count();
        }

        $maxTranslationsCount = $languages->max('translations_count');
        $maxShareableTranslationsCount = $languages->max('shareable_translations_count');
        $maxTotalJsonTranslations = $languages->max(function ($language) {
            return $this->translationService->countJsonByLocale($language->locale);
        });
        $maxTotalDatatableTranslations = $languages->max(function ($language) {
            return $this->translationService->countDatatablesJsonByLocale($language->locale);
        });

        foreach ($languages as $language) {
            $language->total_json_translations = $this->translationService->countJsonByLocale($language->locale);
            $language->total_datatable_translations = $this->translationService->countDatatablesJsonByLocale($language->locale);
            $language->status = (
                $language->translations_count == $maxTranslationsCount &&
                $language->shareable_translations_count == $maxShareableTranslationsCount &&
                $language->total_json_translations == $maxTotalJsonTranslations &&
                $language->total_datatable_translations == $maxTotalDatatableTranslations
            ) ? translate('stable') : translate('missing');
        }

        return $languages;
    }
}
