<?php

use App\Helpers\TranslateHelper;

if (!function_exists('translate')) {
    function translate($key)
    {
        return app(TranslateHelper::class)->translate($key);
    }
}
