<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class CryptHelper
{
    public static function encrypt($string)
    {
        return Crypt::encrypt($string);
    }

    public static function decrypt($string)
    {
        return Crypt::decrypt($string);
    }
}
