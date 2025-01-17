<?php

namespace Modules\Development\Services;

use Modules\Development\Entities\Configuration;
use Modules\Development\Repositories\ConfigurationInterface;

class ConfigurationService
{
    protected $repository;
    public $model;

    public function __construct(ConfigurationInterface $repository, Configuration $configuration)
    {
        $this->model = $configuration;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getByKey($key)
    {
        return $this->repository->getByKey($key);
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

