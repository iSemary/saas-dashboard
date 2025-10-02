<?php 

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class EnumHelper {
    public static function getEnumFromTable($table, $column) {
        try {
            $result = DB::selectOne("SHOW COLUMNS FROM {$table} WHERE Field = ?", [$column]);
            
            if (!$result || !$result->Type) {
                return [];
            }
            
            $type = $result->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);

            if (isset($matches[1])) {
                return array_map(function ($value) {
                    return trim($value, "'");
                }, explode(',', $matches[1]));
            }

            return [];
        } catch (\Exception $e) {
            // Table doesn't exist or other database error
            return [];
        }
    }
}