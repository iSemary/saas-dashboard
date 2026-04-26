<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\ContactList;

use Modules\SmsMarketing\Infrastructure\Persistence\SmContactListRepositoryInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmContactRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\ContactList\CreateSmContactListDTO;
use Modules\SmsMarketing\Application\DTOs\ContactList\UpdateSmContactListDTO;

class SmContactListUseCase
{
    public function __construct(
        private readonly SmContactListRepositoryInterface $repository,
        private readonly SmContactRepositoryInterface $contactRepo,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateSmContactListDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateSmContactListDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function addContacts(int $listId, array $contactIds): void
    {
        $list = $this->repository->findOrFail($listId);
        $list->contacts()->syncWithoutDetaching($contactIds);
        $list->update(['contacts_count' => $list->contacts()->count()]);
    }

    public function removeContacts(int $listId, array $contactIds): void
    {
        $list = $this->repository->findOrFail($listId);
        $list->contacts()->detach($contactIds);
        $list->update(['contacts_count' => $list->contacts()->count()]);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }

    public function getTableList(array $params)
    {
        return $this->repository->getTableList($params);
    }
}
