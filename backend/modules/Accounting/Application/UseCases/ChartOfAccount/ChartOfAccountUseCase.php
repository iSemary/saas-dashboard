<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\ChartOfAccount;

use Modules\Accounting\Infrastructure\Persistence\ChartOfAccountRepositoryInterface;
use Modules\Accounting\Application\DTOs\ChartOfAccount\CreateChartOfAccountDTO;
use Modules\Accounting\Application\DTOs\ChartOfAccount\UpdateChartOfAccountDTO;

class ChartOfAccountUseCase
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateChartOfAccountDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateChartOfAccountDTO $dto)
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
