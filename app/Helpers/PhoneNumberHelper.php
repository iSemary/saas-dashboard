<?php

namespace App\Helpers;

class PhoneNumberHelper
{
    /** 
     * Before: +20 (10) 27-012-337
     * After: 201027012337
     * 
     * Return phone number after:
     * - Remove spaces
     * - Remove special characters
     * - Remove + 
     */
    public static function clean(string $phone)
    {
        // Remove spaces
        $phone = str_replace(' ', '', $phone);

        // Remove special characters and English Characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        return $phone;
    }
}
