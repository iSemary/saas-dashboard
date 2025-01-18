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
}
