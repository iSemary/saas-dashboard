<?php

namespace Modules\Tenant\Services;

use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Repositories\TenantInterface;

class TenantService
{
    protected $repository;
    public $model;

    public function __construct(TenantInterface $repository, Tenant $tenant)
    {
        $this->model = $tenant;
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

    public function init(string $customerUsername)
    {
        return $this->repository->init($customerUsername);
    }
}
