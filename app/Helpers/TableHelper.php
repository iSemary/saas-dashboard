<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class TableHelper
{
    public static function loopOverDates($type, $q, $table, $date)
    {
        switch ($type) {
                // today
            case 1:
                return $q->whereDate($table . '.created_at', date('d-m-Y'));
                break;
                // last_week
            case 2:
                return $q->whereBetween(
                    $table . '.created_at',
                    [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
                );
                break;
                // last_month
            case 3:
                return $q->whereDate($table . '.created_at', '>=', Carbon::now()->subDays(31)->toDateTimeString());
                break;
                // current_year
            case 4:
                return $q->whereYear($table . '.created_at', date('Y'));
                break;
                // from_to_date
            case 5:
                return $q->whereBetween($table . '.created_at', [$date[0], $date[1]]);
                break;
            default:
                return $q->whereDate($table . '.created_at', $date);
                break;
        }
    }

    public static function actionButtons($row, $editRoute, $deleteRoute, $type, $titleType, $showIconsOnly = false)
    {
        $btn = '';

        // Edit button
        if (Gate::allows('update.' . $type)) {
            $btn .= '<button type="button" data-modal-title="Edit ' . $titleType . '" data-modal-link="' . route($editRoute, $row->id) . '" class="btn-info mx-1 btn-sm open-edit-modal">';
            $btn .= $showIconsOnly ? '<i class="far fa-edit"></i>' : '<i class="far fa-edit fa-fw"></i> ' . translate('edit');
            $btn .= '</button>';
        }

        // Delete button
        if (Gate::allows('delete.' . $type)) {
            $btn .= '<button type="button" data-delete-type="' . __($titleType) . '" data-url="' . route($deleteRoute, $row->id) . '" class="btn-danger btn-sm delete-btn">';
            $btn .= $showIconsOnly ? '<i class="fa fa-trash"></i>' : '<i class="fa fa-trash fa-fw"></i> ' . translate('delete');
            $btn .= '</button>';
        }

        return $btn;
    }
}
