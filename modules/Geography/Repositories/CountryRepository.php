<?php

namespace Modules\Geography\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\DB;
use Modules\Geography\Entities\Country;
use Yajra\DataTables\DataTables;

class CountryRepository implements CountryInterface
{
    protected $model;

    public function __construct(Country $country)
    {
        $this->model = $country;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function datatables()
    {
        $rows = $this->model->query()->withTrashed()
            ->leftJoin("provinces", function ($join) {
                $join->on("provinces.country_id", "=", "countries.id")
                    ->where("provinces.is_capital", true);
            })
            ->select([
                "countries.*",
                "provinces.name as capital_province"
            ])->where(
                function ($q) {
                    if (request()->from_date && request()->to_date) {
                        TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                    }
                }
            );

        return DataTables::of($rows)
            ->editColumn('flag', function ($row) {
                return '<img src="' . $row->flag . '" class="img-thumbnail" width="50px" height="50px">';
            })
            ->filterColumn('capital_province', function ($query, $keyword) {
                $query->whereRaw('LOWER(provinces.name) LIKE ?', ["%{$keyword}%"]);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.countries.edit',
                    deleteRoute: 'landlord.countries.destroy',
                    restoreRoute: 'landlord.countries.restore',
                    type: $this->model->pluralTitle,
                    titleType: $this->model->singleTitle,
                    showIconsOnly: false
                );
            })
            ->rawColumns(['flag', 'actions'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function getTimeZones()
    {
        return $this->model->query()
            ->join('provinces', 'provinces.country_id', '=', 'countries.id')
            ->select([
                'provinces.id',
                DB::raw('CONCAT(countries.name, "/", provinces.name, " (GMT", provinces.timezone, ")") as title'),
                'provinces.timezone'
            ])
            ->whereNotNull('provinces.timezone')
            ->get();
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
