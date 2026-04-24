<?php

namespace Modules\Geography\Services;

use Modules\Geography\DTOs\CreateStreetData;
use Modules\Geography\Entities\Street;
use Modules\Geography\Repositories\StreetInterface;

class StreetService
{
    protected $repository;
    public $model;

    public function __construct(StreetInterface $repository, Street $street)
    {
        $this->model = $street;
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

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(CreateStreetData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'town_id' => $data->town_id,
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

