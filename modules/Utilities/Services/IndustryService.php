<?php

namespace Modules\Utilities\Services;

use Modules\Utilities\Entities\Industry;
use Modules\Utilities\Repositories\IndustryInterface;

class IndustryService
{
    protected $repository;
    public $model;

    public function __construct(IndustryInterface $repository, Industry $industry)
    {
        $this->model = $industry;
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

    public function restore($id)
    {
        return $this->repository->restore($id);
    }
}

