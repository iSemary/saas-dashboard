<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\CrmWebhook;

interface CrmWebhookRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): CrmWebhook;
    public function create(array $data): CrmWebhook;
    public function update(int $id, array $data): CrmWebhook;
    public function delete(int $id): bool;
    public function getActive(): Collection;
    public function getForEvent(string $event): Collection;
}
