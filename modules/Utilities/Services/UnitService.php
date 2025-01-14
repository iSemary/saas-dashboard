<?php

namespace Modules\Utilities\Services;

use Modules\Utilities\Entities\Unit;
use Modules\Utilities\Repositories\UnitInterface;

class UnitService
{
    protected $repository;
    public $model;

    public function __construct(UnitInterface $repository, Unit $unit)
    {
        $this->model = $unit;
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
