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

if (!function_exists('render_number')) {
    function render_number($number)
    {
        // Define the thresholds for formatting
        $thresholds = [
            1000000000000000000000000000000 => 'd', // Decillion
            1000000000000000000000000000 => 'n', // Nonillion
            1000000000000000000000000 => 'o', // Octillion
            1000000000000000000000 => 'S', // Septillion
            1000000000000000000 => 's', // Sextillion
            1000000000000000 => 'Q', // Quintillion
            1000000000000 => 'q', // Quadrillion
            1000000000 => 't', // Trillion
            1000000 => 'b', // Billion
            1000 => 'm', // Million
        ];

        // Initialize the formatted number and title
        $formattedNumber = $number;
        $title = number_format($number); // Format the original number with commas

        // Apply formatting based on the number's magnitude
        foreach ($thresholds as $threshold => $suffix) {
            if ($number >= $threshold) {
                $formattedNumber = floor($number / $threshold) . translate('numbers.' . $suffix);
                break;
            }
        }

        // Return the <span> element with the formatted number and title
        return '<span title="' . htmlspecialchars($title) . '">' . htmlspecialchars($formattedNumber) . '</span>';
    }
}
