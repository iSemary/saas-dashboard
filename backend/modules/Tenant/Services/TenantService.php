<?php

namespace Modules\Tenant\Services;

use Modules\Tenant\DTOs\CreateTenantData;
use Modules\Tenant\DTOs\UpdateTenantData;
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

    public function list(array $filters = [], int $perPage = 50)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Tenant
    {
        return Tenant::findOrFail($id);
    }

    public function create(CreateTenantData $data): Tenant
    {
        return $this->repository->create([
            'name' => $data->name,
            'domain' => $data->domain,
            'database_name' => $data->database_name,
            'is_active' => $data->is_active ?? true,
        ]);
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function update($id, UpdateTenantData $data)
    {
        return $this->repository->update($id, $data->toArray());
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

    public function reMigrate($id)
    {
        return $this->repository->reMigrate($id);
    }

    public function seedDatabase($id)
    {
        return $this->repository->seedDatabase($id);
    }

    public function reSeedDatabase($id)
    {
        return $this->repository->reSeedDatabase($id);
    }

    public function getDatabaseHealth($id)
    {
        return $this->repository->getDatabaseHealth($id);
    }
}
