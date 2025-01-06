<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
use Modules\Utilities\Entities\Type;
use Yajra\DataTables\DataTables;

class TypeRepository implements TypeInterface
{
    protected $model;

    public function __construct(Type $type)
    {
        $this->model = $type;
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
            ->editColumn('icon', function ($row) {
                return '<img src="' . $row->icon . '" width="50px" height="50px" alt="type" />';
            })
            ->editColumn('status', function ($row) {
                return translate($row->status);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    $row,
                    'landlord.types.edit',
                    'landlord.types.destroy',
                    $this->model->pluralTitle,
                    $this->model->singleTitle,
                );
            })
            ->rawColumns(['icon', 'actions'])
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
