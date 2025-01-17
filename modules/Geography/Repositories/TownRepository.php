<?php

namespace Modules\Geography\Repositories;

use App\Helpers\TableHelper;
use Modules\Geography\Entities\Town;
use Yajra\DataTables\DataTables;

class TownRepository implements TownInterface
{
    protected $model;

    public function __construct(Town $town)
    {
        $this->model = $town;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows =  $this->model->query()
            ->leftJoin("cities", "cities.id", "=", "towns.city_id")
            ->leftJoin("provinces", "provinces.id", "=", "cities.province_id")
            ->leftJoin("countries", "countries.id", "=", "provinces.country_id")
            ->select([
                "towns.*",
                "provinces.name as province",
                "countries.name as country",
                "cities.name as city",
            ])->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->filterColumn('city', function ($query, $keyword) {
                $query->whereRaw('LOWER(cities.name) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('country', function ($query, $keyword) {
                $query->whereRaw('LOWER(countries.name) LIKE ?', ["%{$keyword}%"]);
            })
            ->filterColumn('province', function ($query, $keyword) {
                $query->whereRaw('LOWER(provinces.name) LIKE ?', ["%{$keyword}%"]);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.towns.edit',
                    deleteRoute: 'landlord.towns.destroy',
                    restoreRoute: 'landlord.towns.restore',
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
