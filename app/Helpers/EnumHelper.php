<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class EnumHelper {
    public static function getEnumFromTable($table, $column) {
        $type = DB::selectOne("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column])->Type;

        preg_match('/^enum\((.*)\)$/', $type, $matches);

        if (isset($matches[1])) {
            return array_map(function ($value) {
                return trim($value, "'");
            }, explode(',', $matches[1]));
        }

        return [];
    }
}