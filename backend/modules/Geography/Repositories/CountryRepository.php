<?php

namespace Modules\Geography\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
                        $q->whereBetween($this->model->getTable() . '.created_at', [request()->from_date, request()->to_date]);
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
                $btn = '';
                $type = $this->model->pluralTitle;
                $titleType = $this->model->singleTitle;

                if (!isset($row->deleted_at) && !$row->deleted_at && Gate::allows('update.' . $type)) {
                    $btn .= '<button type="button" data-modal-title="' . translate("edit") . " " . translate($titleType) . '" data-modal-link="' . route('landlord.countries.edit', $row->id) . '" class="btn-primary mx-1 btn-sm open-edit-modal"><i class="far fa-edit fa-fw"></i> ' . translate('edit') . '</button>';
                }

                if (!isset($row->deleted_at) && !$row->deleted_at && Gate::allows('delete.' . $type)) {
                    $btn .= '<button type="button" data-delete-type="' . translate($titleType) . '" data-url="' . route('landlord.countries.destroy', $row->id) . '" class="btn-danger mx-1 btn-sm delete-btn"><i class="fa fa-trash fa-fw"></i> ' . translate('delete') . '</button>';
                }

                if (isset($row->deleted_at) && $row->deleted_at && Gate::allows('restore.' . $type)) {
                    $btn .= '<button type="button" data-restore-type="' . translate($titleType) . '" data-url="' . route('landlord.countries.restore', $row->id) . '" class="btn-warning mx-1 text-white btn-sm restore-btn"><i class="fas fa-redo-alt fa-fw"></i> ' . translate('restore') . '</button>';
                }

                return $btn;
            })
            ->rawColumns(['flag', 'actions'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model->query();
        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
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
