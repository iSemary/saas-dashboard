<?php

namespace Modules\Geography\Repositories;

use App\Helpers\TableHelper;
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
        $rows = $this->model->query()
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
            ->filterColumn('capital_province', function ($query, $keyword) {
                $query->whereRaw('LOWER(provinces.name) LIKE ?', ["%{$keyword}%"]);
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    $row,
                    'landlord.countries.edit',
                    'landlord.countries.destroy',
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
