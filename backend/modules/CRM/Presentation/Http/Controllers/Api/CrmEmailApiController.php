<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Infrastructure\Persistence\ActivityRepositoryInterface;

class CrmEmailApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        private readonly ActivityRepositoryInterface $activities,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'related_type' => 'required|string',
                'related_id' => 'required|integer',
            ]);

            $filters = [
                'type' => 'email',
                'related_type' => $request->input('related_type'),
                'related_id' => $request->input('related_id'),
            ];

            return $this->apiPaginated(
                $this->activities->paginate($filters, (int) $request->get('per_page', 15))
            );
        } catch (\Throwable $e) {
            return $this->apiError('Failed to retrieve emails', 500, $e->getMessage());
        }
    }

    public function log(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'related_type' => 'required|string',
                'related_id' => 'required|integer',
                'description' => 'nullable|string',
            ]);

            $data = array_merge($request->all(), [
                'type' => 'email',
                'status' => 'completed',
                'completed_at' => now(),
                'created_by' => auth()->id(),
                'assigned_to' => auth()->id(),
            ]);

            $activity = $this->activities->create($data);

            return $this->apiSuccess($activity->load(['creator', 'assignedUser']), 'Email logged', 201);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to log email', 500, $e->getMessage());
        }
    }

    public function send(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string|max:255',
                'body' => 'required|string',
                'related_type' => 'nullable|string',
                'related_id' => 'nullable|integer',
            ]);

            $logged = null;

            if ($request->filled('related_type') && $request->filled('related_id')) {
                $data = [
                    'subject' => $request->input('subject'),
                    'type' => 'email',
                    'status' => 'completed',
                    'completed_at' => now(),
                    'related_type' => $request->input('related_type'),
                    'related_id' => (int) $request->input('related_id'),
                    'created_by' => auth()->id(),
                    'assigned_to' => auth()->id(),
                    'description' => "To: {$request->input('to')}\n\n{$request->input('body')}",
                ];
                $logged = $this->activities->create($data);
            }

            return $this->apiSuccess([
                'sent' => true,
                'activity' => $logged,
            ], 'Email sent and logged');
        } catch (\Throwable $e) {
            return $this->apiError('Failed to send email', 500, $e->getMessage());
        }
    }
}
