<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasDateFilters
{
    /**
     * Apply date filters to query
     *
     * @param mixed $query
     * @param string $table
     * @param int $type Filter type (1=today, 2=last_week, 3=last_month, 4=current_year, 5=from_to_date)
     * @param array|null $date Date range for from_to_date filter
     * @return mixed
     */
    public function filterByDate($query, string $table, int $type, ?array $date = null)
    {
        $column = $table . '.created_at';

        switch ($type) {
            case 1: // today
                return $query->whereDate($column, date('d-m-Y'));
            case 2: // last_week
                return $query->whereBetween(
                    $column,
                    [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
                );
            case 3: // last_month
                return $query->whereDate($column, '>=', Carbon::now()->subDays(31)->toDateTimeString());
            case 4: // current_year
                return $query->whereYear($column, date('Y'));
            case 5: // from_to_date
                return $query->whereBetween($column, [$date[0], $date[1]]);
            default:
                return $query->whereDate($column, $date);
        }
    }
}
