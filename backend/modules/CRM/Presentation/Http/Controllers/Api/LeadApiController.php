<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\CRM\Application\DTOs\CreateLeadDTO;
use Modules\CRM\Application\DTOs\UpdateLeadDTO;
use Modules\CRM\Application\UseCases\Lead\CreateLeadUseCase;
use Modules\CRM\Application\UseCases\Lead\UpdateLeadUseCase;
use Modules\CRM\Application\UseCases\Lead\DeleteLeadUseCase;
use Modules\CRM\Application\UseCases\Lead\GetLeadUseCase;
use Modules\CRM\Application\UseCases\Lead\ListLeadsUseCase;
use Modules\CRM\Application\UseCases\Lead\ConvertLeadUseCase;

class LeadApiController extends Controller implements HasMiddleware
{
    use ApiResponseEnvelope;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.crm.leads', only: ['index', 'show']),
            new Middleware('permission:create.crm.leads', only: ['store']),
            new Middleware('permission:update.crm.leads', only: ['update']),
            new Middleware('permission:delete.crm.leads', only: ['destroy']),
            new Middleware('permission:convert.crm.leads', only: ['convert']),
        ];
    }

    public function __construct(
        private readonly CreateLeadUseCase $createLead,
        private readonly UpdateLeadUseCase $updateLead,
        private readonly DeleteLeadUseCase $deleteLead,
        private readonly GetLeadUseCase $getLead,
        private readonly ListLeadsUseCase $listLeads,
        private readonly ConvertLeadUseCase $convertLead,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'source', 'assigned_to', 'search']);
            $perPage = (int) $request->get('per_page', 15);
            return $this->apiPaginated($this->listLeads->execute($filters, $perPage));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve leads', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'status' => 'nullable|string',
                'source' => 'nullable|string',
            ]);
            $dto = CreateLeadDTO::fromArray($request->all());
            $lead = $this->createLead->execute($dto, auth()->id());
            return $this->apiSuccess($lead->load(['assignedUser', 'creator']), 'Lead created successfully', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create lead', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->getLead->execute($id)->load(['assignedUser', 'creator', 'activities']));
        } catch (\Throwable $e) {
            return $this->apiError('Lead not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateLeadDTO::fromArray($id, $request->all());
            $lead = $this->updateLead->execute($dto, auth()->id());
            return $this->apiSuccess($lead->load(['assignedUser', 'creator']), 'Lead updated successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update lead', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteLead->execute($id);
            return $this->apiSuccess(null, 'Lead deleted successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete lead', 500, $e->getMessage());
        }
    }

    public function convert(Request $request, int $id): JsonResponse
    {
        try {
            $opportunity = $this->convertLead->execute($id, $request->all(), auth()->id());
            return $this->apiSuccess($opportunity, 'Lead converted to opportunity successfully');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to convert lead', 500, $e->getMessage());
        }
    }
}
