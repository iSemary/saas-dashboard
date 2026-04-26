<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\FiscalYear;

use Modules\Accounting\Infrastructure\Persistence\FiscalYearRepositoryInterface;
use Modules\Accounting\Application\DTOs\FiscalYear\CreateFiscalYearDTO;
use Modules\Accounting\Application\DTOs\FiscalYear\UpdateFiscalYearDTO;
use Modules\Accounting\Domain\ValueObjects\FiscalYearStatus;

class FiscalYearUseCase
{
    public function __construct(
        private readonly FiscalYearRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateFiscalYearDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateFiscalYearDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function close(int $id): void
    {
        $fiscalYear = $this->repository->findOrFail($id);
        $fiscalYear->transitionStatus(FiscalYearStatus::CLOSED);
    }

    public function lock(int $id): void
    {
        $fiscalYear = $this->repository->findOrFail($id);
        $fiscalYear->transitionStatus(FiscalYearStatus::LOCKED);
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
