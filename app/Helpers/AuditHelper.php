<?php

namespace App\Helpers;

class AuditHelper
{
    public static function formatChanges($oldValues, $newValues)
    {
        $changes = [];

        if (!empty($newValues)) {
            foreach ($newValues as $key => $value) {
                $oldVal = $oldValues[$key] ?? null;
                if ($oldVal !== $value) {
                    $changes[] = [
                        'field' => $key,
                        'old' => $oldVal,
                        'new' => $value
                    ];
                }
            }
        }

        return $changes;
    }
}
