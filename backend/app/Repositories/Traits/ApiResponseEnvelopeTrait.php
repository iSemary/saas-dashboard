<?php

namespace App\Repositories\Traits;

trait ApiResponseEnvelopeTrait
{
    /**
     * Return a success response.
     * If data is a paginator, it automatically formats it for pagination.
     */
    protected function success(mixed $data = null, string $message = '', int $httpCode = 200): \Illuminate\Http\JsonResponse
    {
        if ($data instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            return $this->paginated($data, $message);
        }

        return response()->json([
            'status' => 'success',
            'code' => $httpCode,
            'success' => true,
            'data' => $data,
            'message' => $message,
            'timestamp' => time(),
        ], $httpCode);
    }

    /**
     * Return an error response.
     */
    protected function error(string $message = '', int $httpCode = 500, mixed $errors = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $httpCode,
            'success' => false,
            'data' => null,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => time(),
        ], $httpCode);
    }

    /**
     * Return a paginated response.
     */
    protected function paginated($paginator, string $message = ''): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'success' => true,
            'data' => $paginator->items(),
            'message' => $message,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'timestamp' => time(),
        ]);
    }
}
