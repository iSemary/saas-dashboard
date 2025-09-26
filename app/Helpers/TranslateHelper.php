<?php

namespace App\Helpers;

use Modules\Localization\Entities\Language;
use Modules\Localization\Repositories\LanguageInterface;
use Modules\Localization\Repositories\TranslationInterface;
use Session;

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

    public static function getLocale($locale = null)
    {
        if ($locale) {
            $locale = $locale;
        } else {
            try {
                if (auth()->check()) {
                    $locale = Session::get('locale');
                    if (!$locale) {
                        $locale = auth()->user()->language?->locale;
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
                if (auth()->check()) {
                    $language = Session::get('language');
                    if (!$language) {
                        $language = auth()->user()->language;
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
