<?php

use App\Helpers\TranslateHelper;
use Illuminate\Support\Facades\Vite;
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

if (!function_exists('isRunningViteDevServer')) {
    function isRunningViteDevServer()
    {
        $url = env('VITE_DEV_SERVER_URL', 'http://localhost:5173');
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1); // Short timeout to avoid delays
        $response = curl_exec($curl);
        $error = curl_errno($curl);
        curl_close($curl);

        // Check if the server responded without errors
        return !$error && $response !== false;
    }
}
