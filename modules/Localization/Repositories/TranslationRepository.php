<?php

namespace Modules\Localization\Repositories;

use App\Helpers\TableHelper;
use App\Services\CacheService;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;
use Yajra\DataTables\DataTables;

class TranslationRepository implements TranslationInterface
{
    protected $model;

    public function __construct(Translation $translation)
    {
        $this->model = $translation;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows =  $this->model->query()
            ->leftJoin("languages", function ($join) {
                $join->on("languages.id", "=", "translations.language_id");
            })->select([
                'translations.*',
                'languages.name as language'
            ])->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->filterColumn('language', function ($query, $keyword) {
                $query->whereRaw('LOWER(languages.name) LIKE ?', ["%{$keyword}%"]);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.translations.edit',
                    deleteRoute: 'landlord.translations.destroy',
                    restoreRoute: 'landlord.translations.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: false
                );
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getByKey($key, $locale = null)
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $cacheValue = $this->getByKeyByCache($key, $locale);
        if ($cacheValue) {
            app('log')->info(self::class . "|Cache Got translation key: $key value: $cacheValue");
            return $cacheValue;
        }
        $databaseRow = $this->getByKeyByDatabase($key, $locale);
        if ($databaseRow) {
            CacheService::forever("translation_{$locale}_{$databaseRow->translation_key}", $databaseRow->translation_value);
            app('log')->info(self::class . "|DB Got translation key: $key value: $databaseRow->translation_value");
            return $databaseRow->translation_value;
        }
        app('log')->info(self::class . "|UNKNOWN translation key: $key");
        return $this->generateTranslation($key, $locale);
    }

    private function generateTranslation($key, $locale = null)
    {
        $value = str_replace(['_', '.'], ' ', $key);
        $value = ucwords($value);
        $value = trim($value);

        $data = [
            'language_id' => 1, // as english language
            'translation_key' => $key,
            'translation_value' => $value,
            'translation_context' => null,
        ];

        $this->create($data);

        return $value;
    }

    private function getByKeyByDatabase($key, $locale = null)
    {
        $language = Language::where("locale", $locale)->first();

        return $this->model->where("translation_key", $key)->where("language_id", $language->id)->latest()->first();
    }

    private function getByKeyByCache($key, $locale = null)
    {
        $language = Language::where("locale", $locale)->first();
        return CacheService::get("translation_{$locale}_{$key}");
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        $translation = $this->model->create($data);
        $locale = Language::find($data['language_id'])->locale;
        CacheService::forever("translation_{$locale}_{$translation->translation_key}", $translation->translation_value);
        return $translation;
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);
            $locale = Language::find($row->language_id)->locale;
            CacheService::forget("translation_{$locale}_{$row->translation_key}");
            CacheService::forever("translation_{$locale}_{$row->translation_key}", $row->translation_value);
            return $row;
        }
        return null;
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        if ($row) {
            $locale = Language::find($row->language_id)->locale;
            CacheService::forget("translation_{$locale}_{$row->translation_key}");
            $row->delete();
            return true;
        }
        return false;
    }
}
