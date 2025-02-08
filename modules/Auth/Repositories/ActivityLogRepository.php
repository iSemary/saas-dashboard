<?php

namespace Modules\Auth\Repositories;

use App\Helpers\IconHelper;
use App\Helpers\TableHelper;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Models\Audit;
use Yajra\DataTables\DataTables;

class ActivityLogRepository implements ActivityLogInterface
{
    protected $model;
    protected $perPage = 10;

    public function __construct(Audit $audit)
    {
        $this->model = $audit;
    }

    public function getDataTablesByRow($id, $model) {
        $rows = $this->model->query()
        ->where('auditable_id', $id)
        ->where('auditable_type', $model);

        return DataTables::of($rows)
            ->editColumn('event', function ($row) {
                return $this->formatEventColumn($row->event);
            })
            ->editColumn('type', function ($row) {
                return translate($row->auditable_type);
            })
            ->editColumn('type_id', function ($row) {
                return ($row->auditable_id);
            })
            ->editColumn('old_values', function ($row) {
                return $this->formatValuesColumn($row->old_values);
            })
            ->editColumn('new_values', function ($row) {
                return $this->formatValuesColumn($row->new_values);
            })
            ->editColumn('ip_address', function ($row) {
                return translate($row->ip_address);
            })
            ->editColumn('user_agent', function ($row) {
                return IconHelper::formatAgentIcons($row->user_agent);
            })
            ->rawColumns([
                'event',
                'type',
                'type_id',
                'old_values',
                'new_values',
                'ip_address',
                'user_agent',
            ])
            ->make(true);
    }

    public function datatables($id)
    {
        $rows =  $this->model->query()->where('user_id', $id);

        return DataTables::of($rows)
            ->editColumn('event', function ($row) {
                return $this->formatEventColumn($row->event);
            })
            ->editColumn('type', function ($row) {
                return translate($row->auditable_type);
            })
            ->editColumn('type_id', function ($row) {
                return ($row->auditable_id);
            })
            ->editColumn('old_values', function ($row) {
                return $this->formatValuesColumn($row->old_values);
            })
            ->editColumn('new_values', function ($row) {
                return $this->formatValuesColumn($row->new_values);
            })
            ->editColumn('ip_address', function ($row) {
                return translate($row->ip_address);
            })
            ->editColumn('user_agent', function ($row) {
                return IconHelper::formatAgentIcons($row->user_agent);
            })
            ->rawColumns([
                'event',
                'type',
                'type_id',
                'old_values',
                'new_values',
                'ip_address',
                'user_agent',
            ])
            ->make(true);
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
        $statusClasses = [
            'created' => 'text-success font-weight-bold',
            'updated' => 'text-primary font-weight-bold',
            'deleted' => 'text-danger font-weight-bold',
            'restored' => 'text-orange font-weight-bold',
        ];

        $class = $statusClasses[$event] ?? 'text-secondary font-weight-bold';
        return "<span class='{$class}'>" . ucfirst($event) . "</span>";
    }

    public function getById() {}
    public function getByAuth() {}
}
