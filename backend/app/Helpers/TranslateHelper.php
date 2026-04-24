<?php

namespace App\Helpers;

use Modules\Localization\Entities\Language;
use Modules\Localization\Repositories\LanguageInterface;
use Modules\Localization\Repositories\TranslationInterface;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TranslateHelper
{
    protected $translationInterface;
    protected $languageInterface;

    public function __construct(TranslationInterface $translationInterface, LanguageInterface $languageInterface)
    {
        $this->translationInterface = $translationInterface;
        $this->languageInterface = $languageInterface;
    }

    public function translate($key, $attributes = [], $locale = null)
    {
        return $this->translationInterface->getByKey($key, $attributes, $locale);
    }

    /**
     * Get translation with JSON-first lookup strategy
     * Priority: JSON file -> Redis cache -> Database
     */
    public function getTranslationWithJsonFirst($key, $attributes = [], $locale = null)
    {
        if (!$locale) {
            $locale = self::getLocale();
        }

        // First, try to get from JSON file
        $jsonTranslation = $this->getFromJsonFile($key, $locale);
        if ($jsonTranslation !== null) {
            return $this->replaceAttributes($jsonTranslation, $attributes);
        }

        // Fallback to existing method (Redis -> Database)
        return $this->translationInterface->getByKey($key, $attributes, $locale);
    }

    /**
     * Get translation from JSON file
     */
    private function getFromJsonFile($key, $locale)
    {
        $jsonPath = public_path("assets/shared/lang/{$locale}.json");
        
        if (!file_exists($jsonPath)) {
            return null;
        }

        try {
            $translations = json_decode(file_get_contents($jsonPath), true);
            return $translations[$key] ?? null;
        } catch (\Exception $e) {
            Log::warning("Failed to read translation JSON file for locale {$locale}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Replace attributes in translation string
     */
    private function replaceAttributes($translation, $attributes = [])
    {
        if (is_array($attributes) && count($attributes)) {
            $modifiedAttributes = array_combine(
                array_map(function ($key) {
                    return ":$key";
                }, array_keys($attributes)),
                array_values($attributes)
            );

            return str_replace(array_keys($modifiedAttributes), array_values($attributes), $translation);
        }

        return $translation;
    }

    public static function getLocale($locale = null)
    {
        if ($locale) {
            $locale = $locale;
        } else {
            try {
                if (Auth::check()) {
                    $locale = Session::get('locale');
                    if (!$locale) {
                        $locale = Auth::user()->language?->locale;
                        if (!$locale) {
                            $locale = app()->getLocale();
                        }
                    }
                } else {
                    $locale = app()->getLocale();
                }
            } catch (\Exception $e) {
                // Database connection not available, use default locale
                $locale = app()->getLocale();
            }
        }

        return $locale;
    }

    public static function getLanguage($language = null)
    {
        if (!$language) {
            try {
                if (Auth::check()) {
                    $language = Session::get('language');
                    if (!$language) {
                        $language = Auth::user()->language;
                        if (!$language) {
                            $language = Language::whereLocale(app()->getLocale())->first();
                        }
                    }
                } else {
                    $language = Language::whereLocale(app()->getLocale())->first();
                }
            } catch (\Exception $e) {
                // Database connection not available, use default language
                $language = Language::whereLocale(app()->getLocale())->first();
            }
        }

        return $language;
    }

    public static function returnTranslatableEditor($model, $column): string
    {
        $objectType = CryptHelper::encrypt(get_class($model));
        $objectKey = CryptHelper::encrypt($column);
        $translationManager = translate("translation_manager");
        $modalLink = route('translations.object.show', $model->id) . '?object_type=' . $objectType . '&object_key=' . $objectKey;

        $btn = '<div class="translatable-editor">';
        $btn .= '<span class="translatable-text">' . htmlspecialchars($model->{$column}, ENT_QUOTES, 'UTF-8') . '</span>';
        $btn .= '<button title="' . htmlspecialchars($translationManager, ENT_QUOTES, 'UTF-8') . '" data-modal-title="' . htmlspecialchars($translationManager, ENT_QUOTES, 'UTF-8') . '" data-modal-link="' . htmlspecialchars($modalLink, ENT_QUOTES, 'UTF-8') . '" class="btn btn-sm translatable-icon open-details-btn" type="button">';
        $btn .= '<i class="fas fa-language"></i>';
        $btn .= '</button></div>';

        return $btn;
    }
}
