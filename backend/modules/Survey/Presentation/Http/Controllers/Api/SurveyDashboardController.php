<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Modules\Survey\Infrastructure\Persistence\SurveyRepositoryInterface;

class SurveyDashboardController extends ApiController
{
    public function __construct(
        private SurveyRepositoryInterface $repository
    ) {}

    public function index(): JsonResponse
    {
        $userId = auth()->id();

        $metrics = [
            'total_surveys' => $this->repository->count(['created_by' => $userId]),
            'active_surveys' => $this->repository->count(['status' => 'active', 'created_by' => $userId]),
            'draft_surveys' => $this->repository->count(['status' => 'draft', 'created_by' => $userId]),
            'closed_surveys' => $this->repository->count(['status' => 'closed', 'created_by' => $userId]),
        ];

        $recentSurveys = $this->repository->list([
            'created_by' => $userId,
        ]);

        return $this->respondWithArray([
            'metrics' => $metrics,
            'recent_surveys' => array_slice($recentSurveys, 0, 5),
        ]);
    }
}
