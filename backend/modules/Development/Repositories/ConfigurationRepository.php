<?php

namespace Modules\Development\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Development\Entities\Configuration;
class ConfigurationRepository implements ConfigurationInterface
{
    protected $model;

    public function __construct(Configuration $configuration)
    {
        $this->model = $configuration;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function getByKey($key)
    {
        return $this->model->where('configuration_key', $key)->first();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('configuration_key', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['is_system'])) {
            $query->where('is_system', $filters['is_system']);
        }

        if (isset($filters['is_visible'])) {
            $query->where('is_visible', $filters['is_visible']);
        }

        return $query->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $configuration = $this->find($id);
        $configuration->update($data);
        return $configuration;
    }

    public function delete($id)
    {
        $configuration = $this->find($id);
        return $configuration->delete();
    }

    public function restore($id)
    {
        $configuration = $this->model->withTrashed()->findOrFail($id);
        return $configuration->restore();
    }
}
