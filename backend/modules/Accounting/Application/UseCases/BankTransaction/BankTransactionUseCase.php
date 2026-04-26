<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\BankTransaction;

use Modules\Accounting\Infrastructure\Persistence\BankTransactionRepositoryInterface;
use Modules\Accounting\Application\DTOs\BankTransaction\CreateBankTransactionDTO;
use Modules\Accounting\Application\DTOs\BankTransaction\UpdateBankTransactionDTO;

class BankTransactionUseCase
{
    public function __construct(
        private readonly BankTransactionRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateBankTransactionDTO $dto)
    {
        $transaction = $this->repository->create($dto->toArray());

        // Update bank account balance
        $transaction->bankAccount->updateBalance();

        return $transaction;
    }

    public function update(int $id, UpdateBankTransactionDTO $dto)
    {
        $transaction = $this->repository->update($id, $dto->toArray());
        $transaction->bankAccount->updateBalance();
        return $transaction;
    }

    public function delete(int $id): bool
    {
        $transaction = $this->repository->findOrFail($id);
        $bankAccount = $transaction->bankAccount;
        $result = $this->repository->delete($id);
        $bankAccount->updateBalance();
        return $result;
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
