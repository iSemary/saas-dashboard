<?php

namespace Modules\Utilities\Repositories;

use App\Helpers\TableHelper;
use Modules\Utilities\Entities\Unit;
class UnitRepository implements UnitInterface
{
    protected $model;

    public function __construct(Unit $unit)
    {
        $this->model = $unit;
    }

    public function all()
    {
        return $this->model->with('type')->get();
    }

    public function find($id)
    {
        return $this->model->with('type')->find($id);
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

    public function create(array $data)
    {
        $data['is_base_unit'] = isset($data['is_base_unit']) && $data['is_base_unit'] ? true : false;
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $data['is_base_unit'] = isset($data['is_base_unit']) && $data['is_base_unit'] ? true : false;
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

    public function getByType($typeId)
    {
        return $this->model->where('type_id', $typeId)->get();
    }

    public function getBaseUnitForType($typeId)
    {
        return $this->model->where('type_id', $typeId)->where('is_base_unit', true)->first();
    }

    public function convertToBaseUnit($value, $fromUnitId, $toUnitId)
    {
        $fromUnit = $this->find($fromUnitId);
        $toUnit = $this->find($toUnitId);

        if (!$fromUnit || !$toUnit || $fromUnit->type_id !== $toUnit->type_id) {
            return null;
        }

        return ($value * $fromUnit->base_conversion) / $toUnit->base_conversion;
    }
}
