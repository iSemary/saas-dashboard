<?php

namespace Modules\Geography\Services;

use Modules\Geography\Entities\Province;
use Modules\Geography\Repositories\ProvinceInterface;

class ProvinceService
{
    protected $repository;
    public $model;

    public function __construct(ProvinceInterface $repository, Province $province)
    {
        $this->model = $province;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
