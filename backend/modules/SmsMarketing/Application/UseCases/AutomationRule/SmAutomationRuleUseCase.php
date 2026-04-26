<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\AutomationRule;

use Modules\SmsMarketing\Infrastructure\Persistence\SmAutomationRuleRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\AutomationRule\CreateSmAutomationRuleDTO;
use Modules\SmsMarketing\Application\DTOs\AutomationRule\UpdateSmAutomationRuleDTO;

class SmAutomationRuleUseCase
{
    public function __construct(
        private readonly SmAutomationRuleRepositoryInterface $repository,
    ) {}

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findOrFail($id);
    }

    public function create(CreateSmAutomationRuleDTO $dto)
    {
        return $this->repository->create(array_merge($dto->toArray(), ['created_by' => auth()->id()]));
    }

    public function update(int $id, UpdateSmAutomationRuleDTO $dto)
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
