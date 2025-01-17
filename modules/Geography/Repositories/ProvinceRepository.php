<?php

namespace Modules\Geography\Repositories;

use App\Helpers\TableHelper;
use Modules\Geography\Entities\Province;
use Yajra\DataTables\DataTables;

class ProvinceRepository implements ProvinceInterface
{
    protected $model;

    public function __construct(Province $province)
    {
        $this->model = $province;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows =  $this->model->query()->withTrashed()
            ->leftJoin("countries", "countries.id", "=", "provinces.country_id")
            ->select([
                "provinces.*",
                "countries.name as country"
            ])->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->filterColumn('country', function ($query, $keyword) {
                $query->whereRaw('LOWER(countries.name) LIKE ?', ["%{$keyword}%"]);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.provinces.edit',
                    deleteRoute: 'landlord.provinces.destroy',
                    restoreRoute: 'landlord.provinces.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: false
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
        $data['is_capital'] = isset($data['is_capital']) && $data['is_capital'] ? true : false;
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $row = $this->model->find($id);
        if ($row) {
            $data['is_capital'] = isset($data['is_capital']) && $data['is_capital'] ? true : false;
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

    public function restore($id)
    {
        $row = $this->model->withTrashed()->find($id);
        if ($row) {
            $row->restore();
            return true;
        }
        return false;
    }
}
