<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\BankAccount;

use Modules\Accounting\Infrastructure\Persistence\BankAccountRepositoryInterface;
use Modules\Accounting\Application\DTOs\BankAccount\CreateBankAccountDTO;
use Modules\Accounting\Application\DTOs\BankAccount\UpdateBankAccountDTO;

class BankAccountUseCase
{
    public function __construct(
        private readonly BankAccountRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateBankAccountDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateBankAccountDTO $dto)
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
