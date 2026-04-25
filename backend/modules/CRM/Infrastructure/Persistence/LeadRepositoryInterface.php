<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\Lead;

interface LeadRepositoryInterface
{
    /**
     * Get paginated list with optional filters.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find by ID or fail.
     */
    public function findOrFail(int $id): Lead;

    /**
     * Find by ID or return null.
     */
    public function find(int $id): ?Lead;

    /**
     * Create new lead.
     */
    public function create(array $data): Lead;

    /**
     * Update lead.
     */
    public function update(int $id, array $data): Lead;

    /**
     * Delete lead.
     */
    public function delete(int $id): bool;

    /**
     * Bulk delete leads.
     */
    public function bulkDelete(array $ids): int;

    /**
     * Get leads by status.
     *
     * @return Collection<int, Lead>
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get leads by source.
     *
     * @return Collection<int, Lead>
     */
    public function getBySource(string $source): Collection;

    /**
     * Get leads assigned to user.
     *
     * @return Collection<int, Lead>
     */
    public function getAssignedTo(int $userId): Collection;

    /**
     * Search leads.
     *
     * @return Collection<int, Lead>
     */
    public function search(string $query): Collection;

    /**
     * Get count by status.
     */
    public function getCountByStatus(): array;

    /**
     * Get conversion rate.
     */
    public function getConversionRate(): float;

    /**
     * Get statistics.
     */
    public function getStatistics(): array;
}
