<?php

namespace Modules\Ticket\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TicketInterface
{
    public function all();
    public function find($id);
    public function findOrFail(int $id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    
    // Ticket-specific methods
    public function getTicketsByStatus($status);
    public function getKanbanData();
    public function updateStatus($id, $newStatus, $userId, $comment = null);
    public function getTicketsByUser($userId);
    public function getOverdueTickets();
    public function getTicketStats();
    public function searchTickets($query);
}
