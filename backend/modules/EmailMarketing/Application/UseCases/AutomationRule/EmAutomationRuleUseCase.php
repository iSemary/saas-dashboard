<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\AutomationRule;

use Modules\EmailMarketing\Infrastructure\Persistence\EmAutomationRuleRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\AutomationRule\CreateEmAutomationRuleDTO;
use Modules\EmailMarketing\Application\DTOs\AutomationRule\UpdateEmAutomationRuleDTO;

class EmAutomationRuleUseCase
{
    public function __construct(
        private readonly EmAutomationRuleRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateEmAutomationRuleDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateEmAutomationRuleDTO $dto)
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function toggle(int $id): void
    {
        $rule = $this->repository->findOrFail($id);
        $rule->update(['is_active' => ! $rule->is_active]);
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
