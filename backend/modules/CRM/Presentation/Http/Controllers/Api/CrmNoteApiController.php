<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Infrastructure\Persistence\CrmNoteRepositoryInterface;

class CrmNoteApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(private readonly CrmNoteRepositoryInterface $notes) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];
            if ($request->has('related_type') && $request->has('related_id')) {
                $filters['related_type'] = $request->input('related_type');
                $filters['related_id'] = $request->input('related_id');
            }
            return $this->apiPaginated($this->notes->paginate($filters, (int) $request->get('per_page', 15)));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve notes', 500, $e->getMessage());
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'content' => 'required|string',
                'related_type' => 'required|string',
                'related_id' => 'required|integer',
            ]);
            $data = $request->all();
            $data['created_by'] = auth()->id();
            $note = $this->notes->create($data);
            return $this->apiSuccess($note->load('creator'), 'Note created', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to create note', 500, $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->notes->findOrFail($id)->load(['creator', 'related']));
        } catch (\Throwable $e) {
            return $this->apiError('Note not found', 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate(['content' => 'required|string']);
            $note = $this->notes->update($id, $request->only(['content']));
            return $this->apiSuccess($note->load('creator'), 'Note updated');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to update note', 500, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->notes->delete($id);
            return $this->apiSuccess(null, 'Note deleted');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to delete note', 500, $e->getMessage());
        }
    }

    public function getForRelated(string $type, int $id): JsonResponse
    {
        try {
            return $this->apiSuccess($this->notes->getForRelated($type, $id));
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get notes', 500, $e->getMessage());
        }
    }
}
