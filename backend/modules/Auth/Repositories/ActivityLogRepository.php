<?php

namespace Modules\Auth\Repositories;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Models\Audit;
class ActivityLogRepository implements ActivityLogInterface
{
    protected $model;
    protected $perPage = 10;

    public function __construct(Audit $audit)
    {
        $this->model = $audit;
    }

    public function getTimelineData($userId, $page = 1, $type = null)
    {
        // Calculate offset
        $offset = ($page - 1) * $this->perPage;

        // Modify query based on type
        $query = $this->model->where('user_id', $userId);

        if ($type === 'deleted') {
            $query->where('event', 'deleted'); // Filter events where event = 'deleted'
        }

        // Get total count for pagination
        $total = $query->count();

        // Get paginated activities
        $activities = $query
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($this->perPage)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            })
            ->map(function($dayActivities) {
                return $dayActivities->groupBy('auditable_type');
            });

        // Create paginator instance
        $paginator = new LengthAwarePaginator(
            $activities,
            $total,
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'activities' => $activities,
            'pagination' => $paginator
        ];
    }

    private function formatValuesColumn($values)
    {
        // Check if the values are empty or null
        if (empty($values) || is_null($values)) {
            return '-';
        }

        // If values is an array, format it
        if (is_array($values)) {
            $formatted = '';
            foreach ($values as $key => $value) {
                $formatted .= "{$key}: {$value}<br/>";
            }
            return $formatted;
        }

        // Return the values as-is if not an array
        return $values;
    }

    private function formatEventColumn($event)
    {
        $icons = [
            'created' => '<i class="fas fa-plus-circle text-success"></i>',
            'updated' => '<i class="fas fa-edit text-primary"></i>',
            'deleted' => '<i class="fas fa-trash text-danger"></i>',
            'restored' => '<i class="fas fa-history text-warning"></i>'
        ];
        $icon = $icons[$event] ?? '<i class="fas fa-info-circle"></i>';
        return $icon . ' <span class="ml-1">' . ucfirst($event) . '</span>';
    }

    private function formatAgentIcons($agent)
    {
        $icons = '';

        // OS Detection
        if (stripos($agent, 'Linux') !== false) {
            $icons .= '<i class="fab fa-linux" title="Linux"></i> ';
        } elseif (stripos($agent, 'Windows') !== false) {
            $icons .= '<i class="fab fa-windows" title="Windows"></i> ';
        } elseif (stripos($agent, 'Mac') !== false) {
            $icons .= '<i class="fab fa-apple" title="MacOS"></i> ';
        } elseif (stripos($agent, 'Android') !== false) {
            $icons .= '<i class="fab fa-android" title="Android"></i> ';
        } elseif (stripos($agent, 'iPhone') !== false || stripos($agent, 'iPad') !== false) {
            $icons .= '<i class="fab fa-apple" title="iOS"></i> ';
        }

        // Browser Detection
        if (stripos($agent, 'Chrome') !== false) {
            $icons .= '<i class="fab fa-chrome" title="Chrome"></i>';
        } elseif (stripos($agent, 'Firefox') !== false) {
            $icons .= '<i class="fab fa-firefox-browser" title="Firefox"></i>';
        } elseif (stripos($agent, 'Safari') !== false) {
            $icons .= '<i class="fab fa-safari" title="Safari"></i>';
        } elseif (stripos($agent, 'Edge') !== false) {
            $icons .= '<i class="fab fa-edge" title="Edge"></i>';
        } elseif (stripos($agent, 'Opera') !== false) {
            $icons .= '<i class="fab fa-opera" title="Opera"></i>';
        }

        return $icons ?: '<span><i class="fa-solid fa-globe" title="' . $agent . '"></i><span>' . translate("unknown_browser") . '</span>';
    }

    public function getById() {}
    public function getByAuth() {}
}
