<?php

namespace Modules\Geography\Services;

use Modules\Geography\DTOs\CreateTownData;
use Modules\Geography\Entities\Town;
use Modules\Geography\Repositories\TownInterface;

class TownService
{
    protected $repository;
    public $model;

    public function __construct(TownInterface $repository, Town $town)
    {
        $this->model = $town;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateTownData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'city_id' => $data->city_id,
        ]);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }
}

