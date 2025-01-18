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

    public function translate($key, $locale = null)
    {
        return $this->translationInterface->getByKey($key, $locale);
    }

    public static function getLocale($locale = null)
    {
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
}
