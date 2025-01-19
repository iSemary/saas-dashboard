<?php

use App\Helpers\TranslateHelper;
use Modules\Development\Services\ConfigurationService;

if (!function_exists('translate')) {
    function translate($key, $locale = null)
    {
        $locale = TranslateHelper::getLocale($locale);
        return app(TranslateHelper::class)->translate($key, $locale);
    }
}


if (!function_exists('configuration')) {
    function configuration($key)
    {
        return app(ConfigurationService::class)->getByKey($key);
    }
}

if (!function_exists('convertToEnglishNumbers')) {
    function convertToEnglishNumbers($input)
    {
        $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        $englishNumbers = range(0, 9);

        $input = str_replace($arabicNumbers, $englishNumbers, $input);
        $input = str_replace($persianNumbers, $englishNumbers, $input);

        return $input;
    }
}

if (!function_exists('vite_assets')) {
    function vite_assets($entry) {
        $manifest = public_path('build/manifest.json');
        
        if (!file_exists($manifest)) {
            // Return empty string or null when manifest doesn't exist
            return '';
        }
        
        return vite($entry);
    }
}