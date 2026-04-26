<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\ContactList;

use Modules\EmailMarketing\Infrastructure\Persistence\EmContactListRepositoryInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmContactRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\ContactList\CreateEmContactListDTO;
use Modules\EmailMarketing\Application\DTOs\ContactList\UpdateEmContactListDTO;

class EmContactListUseCase
{
    public function __construct(
        private readonly EmContactListRepositoryInterface $repository,
        private readonly EmContactRepositoryInterface $contactRepo,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmContactListDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateEmContactListDTO $dto)
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
