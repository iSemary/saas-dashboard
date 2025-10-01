<?php

namespace Modules\CRM\Services;

use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;
use Modules\CRM\Repositories\LeadRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LeadService
{
    protected $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    /**
     * Get all leads with pagination.
     */
    public function getAllLeads(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->leadRepository->getAll($filters, $perPage);
    }

    /**
     * Get lead by ID.
     */
    public function getLeadById(int $id): ?Lead
    {
        return $this->leadRepository->find($id);
    }

    /**
     * Create a new lead.
     */
    public function createLead(array $data): Lead
    {
        $data['created_by'] = auth()->id();
        
        return $this->leadRepository->create($data);
    }

    /**
     * Update an existing lead.
     */
    public function updateLead(int $id, array $data): bool
    {
        return $this->leadRepository->update($id, $data);
    }

    /**
     * Delete a lead.
     */
    public function deleteLead(int $id): bool
    {
        return $this->leadRepository->delete($id);
    }

    /**
     * Convert lead to opportunity.
     */
    public function convertLeadToOpportunity(int $leadId, array $opportunityData = []): Opportunity
    {
        $lead = $this->getLeadById($leadId);
        
        if (!$lead) {
            throw new \Exception('Lead not found');
        }

        return $lead->convertToOpportunity($opportunityData);
    }

    /**
     * Get leads by status.
     */
    public function getLeadsByStatus(string $status): Collection
    {
        return $this->leadRepository->getByStatus($status);
    }

    /**
     * Get leads by source.
     */
    public function getLeadsBySource(string $source): Collection
    {
        return $this->leadRepository->getBySource($source);
    }

    /**
     * Get leads assigned to user.
     */
    public function getLeadsAssignedTo(int $userId): Collection
    {
        return $this->leadRepository->getAssignedTo($userId);
    }

    /**
     * Get lead statistics.
     */
    public function getLeadStatistics(): array
    {
        return [
            'total' => $this->leadRepository->count(),
            'by_status' => $this->leadRepository->getCountByStatus(),
            'by_source' => $this->leadRepository->getCountBySource(),
            'conversion_rate' => $this->leadRepository->getConversionRate(),
            'this_month' => $this->leadRepository->getThisMonthCount(),
        ];
    }

    /**
     * Search leads.
     */
    public function searchLeads(string $query): Collection
    {
        return $this->leadRepository->search($query);
    }

    /**
     * Assign lead to user.
     */
    public function assignLead(int $leadId, int $userId): bool
    {
        return $this->leadRepository->update($leadId, ['assigned_to' => $userId]);
    }

    /**
     * Update lead status.
     */
    public function updateLeadStatus(int $leadId, string $status): bool
    {
        return $this->leadRepository->update($leadId, ['status' => $status]);
    }
}
