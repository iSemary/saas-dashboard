<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\TaxRate;

use Modules\Accounting\Infrastructure\Persistence\TaxRateRepositoryInterface;
use Modules\Accounting\Application\DTOs\TaxRate\CreateTaxRateDTO;
use Modules\Accounting\Application\DTOs\TaxRate\UpdateTaxRateDTO;

class TaxRateUseCase
{
    public function __construct(
        private readonly TaxRateRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateTaxRateDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateTaxRateDTO $dto)
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
