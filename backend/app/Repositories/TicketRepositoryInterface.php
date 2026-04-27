<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface TicketRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get ticket detail with all relations
     *
     * @param int $id
     * @return Model|null
     */
    public function getDetailWithRelations(int $id): ?Model;

    /**
     * Add comment to ticket
     *
     * @param int $ticketId
     * @param array $data
     * @return Model|null
     */
    public function addComment(int $ticketId, array $data): ?Model;

    /**
     * Assign ticket to user
     *
     * @param int $ticketId
     * @param int $userId
     * @return Model|null
     */
    public function assignToUser(int $ticketId, int $userId): ?Model;

    /**
     * Change ticket status
     *
     * @param int $ticketId
     * @param string $status
     * @param string|null $comment
     * @return Model|null
     */
    public function changeStatus(int $ticketId, string $status, ?string $comment = null): ?Model;

    /**
     * Change ticket priority
     *
     * @param int $ticketId
     * @param string $priority
     * @return Model|null
     */
    public function changePriority(int $ticketId, string $priority): ?Model;

    /**
     * Close ticket
     *
     * @param int $ticketId
     * @return Model|null
     */
    public function close(int $ticketId): ?Model;

    /**
     * Reopen ticket
     *
     * @param int $ticketId
     * @return Model|null
     */
    public function reopen(int $ticketId): ?Model;

    /**
     * Get activity timeline for ticket
     *
     * @param Model $ticket
     * @return array
     */
    public function getActivityTimeline(Model $ticket): array;

    /**
     * Log activity
     *
     * @param Model $ticket
     * @param string $type
     * @param array $data
     * @return void
     */
    public function logActivity(Model $ticket, string $type, array $data = []): void;

    /**
     * Get SLA metrics for ticket
     *
     * @param Model $ticket
     * @return array
     */
    public function getSLAMetrics(Model $ticket): array;

    /**
     * Get tickets by status for kanban view
     *
     * @param string $status
     * @param array $filters
     * @return array
     */
    public function getByStatus(string $status, array $filters = []): array;
}
