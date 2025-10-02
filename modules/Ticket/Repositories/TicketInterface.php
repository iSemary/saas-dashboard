<?php

namespace Modules\Ticket\Repositories;

interface TicketInterface
{
    public function all();
    public function datatables();
    public function find($id);
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
