<?php

namespace Modules\Geography\Repositories;

use App\Helpers\TableHelper;
use Modules\Geography\Entities\City;
class CityRepository implements CityInterface
{
    protected $model;

    public function __construct(City $city)
    {
        $this->model = $city;
    }

    public function all()
    {
        return $this->model->all();
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
        $query = $this->model->query()
            ->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id')
            ->select([
                'cities.*',
                'provinces.name as province_name',
            ]);
        if (isset($filters['search'])) {
            $query->where('cities.name', 'like', "%{$filters['search']}%");
        }
        if (isset($filters['province_id'])) {
            $query->where('cities.province_id', (int) $filters['province_id']);
        }
        return $query->orderBy('cities.created_at', 'desc')->paginate($perPage);
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
