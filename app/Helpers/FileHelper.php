<?php

namespace App\Helpers;

class FileHelper
{
    public static function returnSizeString(int $size = null)
    {
        if ($size === null) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($size, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
