<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery();
        $this->applyFilters($query, $filters);
        return $query->get();
    }

    public function find(int $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();
        $this->applyFilters($query, $filters);
        return $query->paginate($perPage);
    }

    protected function applyFilters($query, array $filters): void
    {
        foreach ($filters as $key => $value) {
            if (in_array($key, ['search', 'per_page', 'page', 'sort_by', 'sort_dir'])) {
                continue;
            }
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }
    }
}
