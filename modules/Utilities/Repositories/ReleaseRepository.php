<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
use Modules\Utilities\Entities\Release;
use Yajra\DataTables\DataTables;

class ReleaseRepository implements ReleaseInterface
{
    protected $model;

    public function __construct(Release $release)
    {
        $this->model = $release;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    $row,
                    'landlord.releases.edit',
                    'landlord.releases.destroy',
                    $this->model->pluralTitle,
                    $this->model->singleTitle,
                );
            })
            ->rawColumns(['actions'])
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

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->update($data);
            return $row;
        }
        return null;
    }

    public function delete($id)
    {
        $row = $this->model->find($id);
        if ($row) {
            $row->delete();
            return true;
        }
        return false;
    }
}
