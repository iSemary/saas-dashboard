<?php

namespace Modules\HR\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\HR\Domain\Entities\Employee;
use Symfony\Component\HttpFoundation\Response;

class EmployeeContextMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();
        $employee = Employee::query()->where('user_id', $userId)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => translate('message.operation_failed'),
            ], 403);
        }

        $request->attributes->set('employee_context', $employee);
        return $next($request);
    }
}
