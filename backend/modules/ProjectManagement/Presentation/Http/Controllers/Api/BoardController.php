<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ProjectManagement\Domain\Entities\BoardColumn;
use Modules\ProjectManagement\Domain\Entities\BoardSwimlane;
use Modules\ProjectManagement\Domain\Entities\Task;
use Modules\ProjectManagement\Infrastructure\Persistence\ProjectRepositoryInterface;

class BoardController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(protected ProjectRepositoryInterface $repository) {}

    public function show(string $projectId): JsonResponse
    {
        $project = $this->repository->findOrFail($projectId);
        $columns = BoardColumn::where('project_id', $projectId)->with('tasks')->orderBy('position')->get();
        $swimlanes = BoardSwimlane::where('project_id', $projectId)->orderBy('position')->get();

        return $this->apiSuccess([
            'project' => $project,
            'columns' => $columns,
            'swimlanes' => $swimlanes,
        ]);
    }

    public function configure(Request $request, string $projectId): JsonResponse
    {
        $project = $this->repository->findOrFail($projectId);

        if ($request->has('columns')) {
            foreach ($request->input('columns') as $colData) {
                BoardColumn::updateOrCreate(['id' => $colData['id'] ?? null], array_merge($colData, ['project_id' => $projectId]));
            }
        }

        if ($request->has('swimlanes')) {
            foreach ($request->input('swimlanes') as $swimData) {
                BoardSwimlane::updateOrCreate(['id' => $swimData['id'] ?? null], array_merge($swimData, ['project_id' => $projectId]));
            }
        }

        return $this->apiSuccess(null, translate('message.action_completed'));
    }
}
