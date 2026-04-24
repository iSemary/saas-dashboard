<?php

namespace Modules\CRM\Services;

use Modules\CRM\DTOs\CreateContactData;
use Modules\CRM\DTOs\UpdateContactData;
use Modules\CRM\Repositories\ContactRepositoryInterface;
use Modules\CRM\Models\Contact;

class ContactService
{
    public function __construct(protected ContactRepositoryInterface $repository) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function findOrFail(int $id): Contact
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateContactData $data): Contact
    {
        $arrayData = $data->toArray();
        $arrayData['created_by'] = auth()->id();
        $arrayData['type'] = $data->type ?? 'individual';
        return $this->repository->create($arrayData);
    }

    public function update(int $id, UpdateContactData $data): Contact
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
