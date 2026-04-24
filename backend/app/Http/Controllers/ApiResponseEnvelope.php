<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * Trait for API responses that match the NextJS frontend's envelope format.
 * Returns: { status: "success"|"error", data: ..., message: "..." }
 */
trait ApiResponseEnvelope
{
    protected function apiSuccess(mixed $data = null, string $message = '', int $httpCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ], $httpCode);
    }

    protected function apiError(string $message = '', int $httpCode = 500, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'data' => null,
            'message' => $message,
            'errors' => $errors,
        ], $httpCode);
    }

    protected function apiPaginated($paginator, string $message = ''): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $paginator->items(),
            'message' => $message,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
