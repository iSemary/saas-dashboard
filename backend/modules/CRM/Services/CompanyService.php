<?php

namespace Modules\CRM\Services;

use Modules\CRM\DTOs\CreateCompanyData;
use Modules\CRM\DTOs\UpdateCompanyData;
use Modules\CRM\Repositories\CompanyRepositoryInterface;
use Modules\CRM\Models\Company;

class CompanyService
{
    public function __construct(protected CompanyRepositoryInterface $repository) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Company
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateCompanyData $data): Company
    {
        $arrayData = $data->toArray();
        $arrayData['created_by'] = auth()->id();
        $arrayData['type'] = $data->type ?? 'customer';
        return $this->repository->create($arrayData);
    }

    public function update(int $id, UpdateCompanyData $data): Company
    {
        return $this->repository->update($id, $data->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }

    public function getActivity(int $id, int $perPage = 20)
    {
        return $this->repository->getActivity($id, $perPage);
    }
}
