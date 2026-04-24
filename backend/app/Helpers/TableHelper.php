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

    public static function actionButtons(
        $row,
        $editRoute = null,
        $deleteRoute = null,
        $restoreRoute = null,
        $type = null,
        $titleType = null,
        $showIconsOnly = false,
        $showActivityLogs = false,
    ) {
        $btn = '';

        // Edit button
        if (!isset($row->deleted_at) && !$row->deleted_at && $editRoute && Gate::allows('update.' . $type)) {
            $btn .= '<button type="button" data-modal-title="' . translate("edit") .  " " . translate($titleType) . '" data-modal-link="' . route($editRoute, $row->id) . '" class="btn-primary mx-1 btn-sm open-edit-modal">';
            $btn .= $showIconsOnly ? '<i class="far fa-edit"></i>' : '<i class="far fa-edit fa-fw"></i> ' . translate('edit');
            $btn .= '</button>';
        }

        // Delete button
        if (!isset($row->deleted_at) && !$row->deleted_at && $deleteRoute && Gate::allows('delete.' . $type)) {
            $btn .= '<button type="button" data-delete-type="' . translate($titleType) . '" data-url="' . route($deleteRoute, $row->id) . '" class="btn-danger mx-1 btn-sm delete-btn">';
            $btn .= $showIconsOnly ? '<i class="fa fa-trash"></i>' : '<i class="fa fa-trash fa-fw"></i> ' . translate('delete');
            $btn .= '</button>';
        }

        // Restore button
        if (isset($row->deleted_at) && $row->deleted_at && $restoreRoute && Gate::allows('restore.' . $type)) {
            $btn .= '<button type="button" data-restore-type="' . translate($titleType) . '" data-url="' . route($restoreRoute, $row->id) . '" class="btn-warning mx-1 text-white btn-sm restore-btn">';
            $btn .= $showIconsOnly ? '<i class="fas fa-redo-alt"></i>' : '<i class="fas fa-redo-alt fa-fw"></i> ' . translate('restore');
            $btn .= '</button>';
        }

        // Activity Logs button
        if ($showActivityLogs) {
            $objectType = CryptHelper::encrypt(get_class($showActivityLogs));
            $btn .= '<button type="button" data-modal-title="' . translate($titleType) . " " . translate("activity_log") . '" data-modal-link="' . route('activity-logs.row', $row->id) . '?object_type=' . $objectType . '" class="btn-teal mx-1 btn-sm open-details-btn">';
            $btn .= $showIconsOnly ? '<i class="fas fa-history"></i>' : '<i class="fas fa-history fa-fw"></i> ' . translate('activity_log');
            $btn .= '</button>';
        }

        return $btn;
    }

    public static function viewMore($originalText, $length, $link = null)
    {
        if (strlen($originalText) <= $length) {
            return htmlspecialchars($originalText);
        }

        $text = strip_tags($originalText);

        if ($link) {
            return htmlspecialchars('<span class="d-inline-block">' . substr($text, 0, $length) . '... <a href="' . $link . '" target="_blank" class="btn-link"><i class="fas fa-external-link-square-alt"></i></a></span>');
        }

        return '<span class="d-inline-block"><span>' . htmlspecialchars(substr($text, 0, $length)) . '</span>... <button type="button" class="btn-primary btn-sm copy-to-clipboard-btn" data-content="' . htmlspecialchars($originalText) . '" title="' . translate('copy_to_clipboard') . '"><i class="far fa-copy"></i></button></span>';
    }
}
