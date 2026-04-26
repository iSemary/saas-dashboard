<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\Contact;

use Modules\SmsMarketing\Infrastructure\Persistence\SmContactRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\Contact\CreateSmContactDTO;
use Modules\SmsMarketing\Application\DTOs\Contact\UpdateSmContactDTO;

class SmContactUseCase
{
    public function __construct(
        private readonly SmContactRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateSmContactDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateSmContactDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
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
