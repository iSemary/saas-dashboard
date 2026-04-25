<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmWebhook;

class CrmWebhookRepository implements CrmWebhookRepositoryInterface
{
    public function __construct(protected CrmWebhook $model) {}
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator { return $this->model->with(['creator'])->paginate($perPage); }
    public function findOrFail(int $id): CrmWebhook { return $this->model->findOrFail($id); }
    public function create(array $data): CrmWebhook { return $this->model->create($data); }
    public function update(int $id, array $data): CrmWebhook { $hook = $this->findOrFail($id); $hook->update($data); return $hook; }
    public function delete(int $id): bool { return $this->model->destroy($id) > 0; }
    public function getActive(): Collection { return $this->model->active()->get(); }
    public function getForEvent(string $event): Collection { return $this->model->active()->forEvent($event)->get(); }
}
