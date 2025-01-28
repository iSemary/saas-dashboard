<?php

namespace Modules\Localization\Repositories;

use App\Console\Commands\TranslateMissingTranslations;
use App\Helpers\TableHelper;
use App\Services\CacheService;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;
use Yajra\DataTables\DataTables;
use RecursiveDirectoryIterator;
use CallbackFilterIterator;
use RecursiveIteratorIterator;

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

    public function exists($key)
    {
        return $this->model->where("translation_key", $key)->whereDoesntHave('translationObjects')->exists();
    }

    public function datatables()
    {
        $rows =  $this->model->query()->withTrashed()->whereDoesntHave('translationObjects')
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
            ->editColumn('is_shareable', function ($row) {
                return $row->is_shareable ? translate('yes') : translate('no');
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.translations.edit',
                    deleteRoute: 'landlord.translations.destroy',
                    restoreRoute: 'landlord.translations.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: true
                );
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getByKey($key, $attributes = [], $locale = null)
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $cacheValue = $this->getByKeyByCache($key, $locale);
        if ($cacheValue) {
            $translationValue = $cacheValue;
        } else {
            $databaseRow = $this->getByKeyByDatabase($key, $locale);
            if ($databaseRow) {
                CacheService::forever("translation_{$locale}_{$databaseRow->translation_key}", $databaseRow->translation_value);
                $translationValue = $databaseRow->translation_value;
            } else {
                app('log')->info(self::class . "|UNKNOWN translation key: $key");
                $translationValue =  $this->generateTranslation($key, 'en');
            }
        }

        // Assign values to attributes
        if (is_array($attributes) && count($attributes)) {
            $modifiedAttributes = array_combine(
                array_map(function ($key) {
                    return ":$key"; // Append ":" to each key
                }, array_keys($attributes)),
                array_values($attributes)
            );

            return str_replace(array_keys($modifiedAttributes), array_values($attributes), $translationValue);
        } else {
            return $translationValue;
        }
    }

    private function generateTranslation($key, $locale = null)
    {
        $value = str_replace(['_', '.'], ' ', $key);
        $value = ucwords($value);
        $value = trim($value);

        $language = Language::where('locale', $locale)->first();
        if (!$language) {
            $language = Language::where("locale", app()->getLocale())->first();
        }

        $existingTranslation = $this->model->where('language_id', $language->id)
            ->where('translation_key', $key)
            ->first();

        if (!$existingTranslation) {
            $data = [
                'language_id' => $language->id,
                'translation_key' => $key,
                'translation_value' => $value,
                'translation_context' => null,
            ];

            $this->create($data);
        }

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
        $data['is_shareable'] = isset($data['is_shareable']) && $data['is_shareable'] ? true : false;
        $translation = $this->model->create($data);
        $locale = Language::find($data['language_id'])->locale;
        CacheService::forever("translation_{$locale}_{$translation->translation_key}", $translation->translation_value);
        if ($data['is_shareable']) {
            $this->generateJson();
        }
        return $translation;
    }

    public function update($id, array $data)
    {
        $data['is_shareable'] = isset($data['is_shareable']) && $data['is_shareable'] ? true : false;
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);

            if ($data['is_shareable']) {
                $this->generateJson();
            }

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

    public function restore($id)
    {
        $row = $this->model->withTrashed()->find($id);
        if ($row) {
            $locale = Language::find($row->language_id)->locale;
            CacheService::forever("translation_{$locale}_{$row->translation_key}", $row->translation_value);
            $row->restore();
            return true;
        }
        return false;
    }

    public function syncMissing()
    {
        app(TranslateMissingTranslations::class)->handle();
        return true;
    }

    public function generateJson()
    {
        try {
            $languages = Language::all();
            $keys = $this->model->select('translation_key')->where("is_shareable", 1)->distinct()->get();
            $translations = [];
            foreach ($languages as $language) {
                $translations[$language->locale] = [];
                foreach ($keys as $key) {
                    $translations[$language->locale][$key->translation_key] = $this->getByKey($key->translation_key, $language->locale);
                }
            }
            $path = public_path('assets/shared/lang');
            foreach ($translations as $locale => $translation) {
                $file = $path . "/$locale.json";
                file_put_contents($file, json_encode($translation, JSON_PRETTY_PRINT));
            }
            return ['success' => true, 'message' => 'JSON files generated successfully.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function countJsonByLocale($locale)
    {
        $file = public_path("assets/shared/lang/{$locale}.json");
        if (!file_exists($file)) {
            return 0;
        }

        $content = file_get_contents($file);
        $translations = json_decode($content, true);
        return count($translations);
    }

    public function countDatatablesJsonByLocale($locale)
    {
        $file = public_path("assets/shared/plugins/DataTables/lang/{$locale}.json");
        if (!file_exists($file)) {
            return 0;
        }

        $content = file_get_contents($file);
        $translations = json_decode($content, true);
        return count($translations);
    }

    public function updateObjectTranslations(string $decryptedObjectType, string $decryptedObjectKey, int $objectId, array $translations)
    {
        foreach ($translations as $languageLocale => $translationValue) {
            $row = $decryptedObjectType::where('id', $objectId)->first();
            $row->setTranslatable($decryptedObjectKey, $translationValue, $languageLocale);
        }
    }

    // Scan all .js files in /public/folder [Excluded (Plugins) folder]
    // then in the $keys array will contain file path and key in the file
    // scan for any thing containing translate.* for ex: translate.name or translate.email 
    // it may contain more than one in the file 
    public function getUsedTranslationInJs()
    {
        $keys = [];

        // Define the directory to scan
        $directory = public_path();

        // Create a RecursiveDirectoryIterator to iterate through the directory
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        // Filter out the Plugins folder
        $filter = function ($file) {
            return $file->isFile() && $file->getExtension() === 'js' && !str_contains($file->getPathname(), 'plugins');
        };

        // Apply the filter
        $filteredIterator = new CallbackFilterIterator($iterator, $filter);

        // Regular expression to match translate.* patterns
        $pattern = '/translate\.([a-zA-Z0-9_]+)/';

        foreach ($filteredIterator as $file) {
            $filePath = $file->getPathname();
            $content = file_get_contents($filePath);

            // Search for translate.* patterns in the file content
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $key) {
                    $keys[] = [
                        'file' => $filePath,
                        'key' => $key,
                        'exists' => $this->exists($key),
                    ];
                }
            }
        }

        // Sort the $keys array by the 'exists' key (false first)
        usort($keys, function ($a, $b) {
            return $a['exists'] <=> $b['exists']; // false comes before true
        });

        return $keys;
    }

    // Scan all .php files in root folder [Excluded (vendor and plugins) folder]
    // then in the $keys array will contain file path and key in the file
    // scan for any thing containing translate(*) for ex: translate(name) or translate(email) 
    // ALSO scan for any thing containing @translate(*) for ex: @translate(name) or @translate(email) 
    // it may contain more than one in the file 
    public function getUsedTranslationInPhp()
    {
        // Define the directory to scan (root folder)
        $directory = base_path();

        // Create a RecursiveDirectoryIterator to iterate through the directory
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        // Filter out the vendor and plugins folders
        $filter = function ($file) {
            return $file->isFile() &&
                $file->getExtension() === 'php' &&
                !str_contains($file->getPathname(), 'vendor') &&
                !str_contains($file->getPathname(), 'plugins');
        };

        // Apply the filter
        $filteredIterator = new CallbackFilterIterator($iterator, $filter);

        // Regular expression to match translate(*) and @translate(*) patterns
        $pattern = '/(@?)translate\(([\'"])([a-zA-Z0-9_]+)\2\)/';

        foreach ($filteredIterator as $file) {
            $filePath = $file->getPathname();
            $content = file_get_contents($filePath);

            // Search for translate(*) and @translate(*) patterns in the file content
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[3] as $key) {
                    $keys[] = [
                        'file' => $filePath,
                        'key' => $key,
                        'exists' => $this->exists($key),
                    ];
                }
            }
        }

        // Sort the $keys array by the 'exists' key (false first)
        usort($keys, function ($a, $b) {
            return $a['exists'] <=> $b['exists']; // false comes before true
        });

        return $keys;
    }
}
