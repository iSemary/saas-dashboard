<?php

namespace Modules\Geography\Services;

use Modules\Geography\DTOs\CreateCityData;
use Modules\Geography\Entities\City;
use Modules\Geography\Repositories\CityInterface;

class CityService
{
    protected $repository;
    public $model;

    public function __construct(CityInterface $repository, City $city)
    {
        $this->model = $city;
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

    public function create(CreateCityData $data)
    {
        return $this->repository->create([
            'name' => $data->name,
            'province_id' => $data->province_id,
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

