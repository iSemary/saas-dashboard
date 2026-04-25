<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmAutomationRule;

class CrmAutomationRuleRepository implements CrmAutomationRuleRepositoryInterface
{
    public function __construct(protected CrmAutomationRule $model) {}
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator { return $this->model->with(['creator'])->paginate($perPage); }
    public function findOrFail(int $id): CrmAutomationRule { return $this->model->findOrFail($id); }
    public function create(array $data): CrmAutomationRule { return $this->model->create($data); }
    public function update(int $id, array $data): CrmAutomationRule { $rule = $this->findOrFail($id); $rule->update($data); return $rule; }
    public function delete(int $id): bool { return $this->model->destroy($id) > 0; }
    public function getActive(): Collection { return $this->model->active()->byPriority()->get(); }
    public function getForEvent(string $event): Collection { return $this->model->active()->forEvent($event)->byPriority()->get(); }
}
