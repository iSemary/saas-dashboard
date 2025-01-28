<?php

namespace App\Helpers;

use Modules\Localization\Repositories\TranslationInterface;

class TranslateHelper
{
    protected $translationInterface;

    public function __construct(TranslationInterface $translationInterface)
    {
        $this->translationInterface = $translationInterface;
    }

    public function translate($key, $attributes = [], $locale = null)
    {
        return $this->translationInterface->getByKey($key, $attributes, $locale);
    }

    public static function getLocale($locale = null)
    {
        // TODO Add get the locale from session and also set the locale in session based on changing in language and login or register 
        if ($locale) {
            $locale = $locale;
        } else {
            if (auth()->check()) {
                $locale = auth()->user()->language?->locale;
                if (!$locale) {
                    $locale = app()->getLocale();
                }
            } else {
                $locale = app()->getLocale();
            }
        }

        return $locale;
    }

    public static function returnTranslatableEditor($model, $column)
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
