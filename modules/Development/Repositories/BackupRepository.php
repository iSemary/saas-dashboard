<?php

namespace Modules\Development\Repositories;

use App\Helpers\TableHelper;
use Modules\Development\Entities\Backup;
use Yajra\DataTables\DataTables;

class BackupRepository implements BackupInterface
{
    protected $model;

    public function __construct(Backup $backup)
    {
        $this->model = $backup;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()->where(
            function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            }
        );

        return DataTables::of($rows)
            ->editColumn('metadata', function ($row) {
                $metadata = $row->metadata; // No need for json_decode
                $formatted = '';

                foreach ($metadata as $key => $value) {
                    $statusClass = $value ? 'text-success' : 'text-danger';
                    $statusText = $value ? 'true' : 'false';
                    $formatted .= "<div><strong>{$key}:</strong> <span class='{$statusClass}'>{$statusText}</span></div>";
                }

                return $formatted;
            })
            ->rawColumns(['metadata'])
            ->make(true);
    }


    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
