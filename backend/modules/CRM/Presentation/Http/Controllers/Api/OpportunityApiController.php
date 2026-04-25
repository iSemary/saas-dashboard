<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Modules\CRM\Application\UseCases\Opportunity\CreateOpportunityUseCase;
use Modules\CRM\Application\UseCases\Opportunity\UpdateOpportunityUseCase;
use Modules\CRM\Application\UseCases\Opportunity\MoveOpportunityStageUseCase;
use Modules\CRM\Application\UseCases\Opportunity\CloseOpportunityWonUseCase;
use Modules\CRM\Application\UseCases\Opportunity\GetPipelineDataUseCase;
use Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface;

class OpportunityApiController extends ApiController implements HasMiddleware
{
    use ApiResponseEnvelope;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.crm.opportunities', only: ['index', 'show', 'pipeline']),
            new Middleware('permission:create.crm.opportunities', only: ['store']),
            new Middleware('permission:update.crm.opportunities', only: ['update', 'moveStage']),
            new Middleware('permission:delete.crm.opportunities', only: ['destroy']),
            new Middleware('permission:close.crm.opportunities', only: ['closeWon']),
        ];
    }

    public function __construct(
        private readonly CreateOpportunityUseCase $createOpportunity,
        private readonly UpdateOpportunityUseCase $updateOpportunity,
        private readonly MoveOpportunityStageUseCase $moveStage,
        private readonly CloseOpportunityWonUseCase $closeWon,
        private readonly GetPipelineDataUseCase $getPipeline,
        private readonly OpportunityRepositoryInterface $opportunities,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['stage', 'assigned_to', 'search']);
            $perPage = (int) $request->get('per_page', 15);
            return $this->apiPaginated($this->opportunities->paginate($filters, $perPage));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve opportunities', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(['name' => 'required|string|max:255', 'stage' => 'nullable|string']);
            $opp = $this->createOpportunity->execute($request->all(), auth()->id());
            return $this->apiSuccess($opp->load(['assignedUser', 'lead', 'contact', 'company']), 'Opportunity created', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create opportunity', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->opportunities->findOrFail($id)->load(['assignedUser', 'lead', 'contact', 'company', 'activities']));
        } catch (\Throwable $e) {
            return $this->apiError('Opportunity not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $opp = $this->updateOpportunity->execute($id, $request->all(), auth()->id());
            return $this->apiSuccess($opp->load(['assignedUser', 'lead', 'contact']), 'Opportunity updated');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update opportunity', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->opportunities->delete($id);
            return $this->apiSuccess(null, 'Opportunity deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete opportunity', 500, $e->getMessage());
        }
    }

    public function pipeline(): JsonResponse
    {
        try {
            return $this->apiSuccess($this->getPipeline->execute());
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get pipeline data', 500, $e->getMessage());
        }
    }

    public function moveStage(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate(['stage' => 'required|string']);
            $opp = $this->moveStage->execute($id, $request->input('stage'), auth()->id());
            return $this->apiSuccess($opp, 'Stage updated');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to move stage', 500, $e->getMessage());
        }
    }

    public function closeWon(int $id): JsonResponse
    {
        try {
            $opp = $this->closeWon->execute($id, auth()->id());
            return $this->apiSuccess($opp, 'Opportunity closed as won');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to close opportunity', 500, $e->getMessage());
        }
    }
}
