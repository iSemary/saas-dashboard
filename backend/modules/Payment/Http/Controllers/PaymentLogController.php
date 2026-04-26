<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Payment\Entities\PaymentGatewayLog;
use Modules\Payment\Entities\PaymentAuditLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PaymentLogController extends ApiController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:read.payment_logs', only: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of payment logs.
     */
    public function index(Request $request)
    {
        return $this->getDataTables($request);
    }

    /**
     * Display the specified payment log.
     */
    public function show($id)
    {
        // Try to find in gateway logs first, then audit logs
        $log = PaymentGatewayLog::find($id);
        if (!$log) {
            $log = PaymentAuditLog::find($id);
        }

        if (!$log) {
            return response()->json(['error' => 'Payment log not found'], 404);
        }

        return response()->json(['data' => $log]);
    }

    /**
     * Get DataTables data for payment logs.
     */
    private function getDataTables(Request $request)
    {
        $logType = $request->get('log_type', 'gateway'); // gateway or audit

        if ($logType === 'audit') {
            $query = PaymentAuditLog::with('user')
                ->orderBy('created_at', 'desc');
        } else {
            $query = PaymentGatewayLog::with(['paymentMethod', 'transaction'])
                ->orderBy('created_at', 'desc');
        }

        // Apply filters
        if ($request->has('level') && $request->level) {
            $query->where('log_level', $request->level);
        }

        if ($request->has('operation') && $request->operation) {
            $query->where('operation', 'like', '%' . $request->operation . '%');
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate($request->get('length', 10));

        $data = $logs->getCollection()->map(function ($log) use ($logType) {
            if ($logType === 'audit') {
                return [
                    'id' => $log->id,
                    'operation' => $log->operation,
                    'entity_type' => $log->entity_type,
                    'user' => $log->user ? $log->user->name : 'System',
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            } else {
                return [
                    'id' => $log->id,
                    'operation' => $log->operation,
                    'log_level' => $log->log_level,
                    'payment_method' => $log->paymentMethod ? $log->paymentMethod->name : 'N/A',
                    'http_status' => $log->http_status,
                    'processing_time' => $log->processing_time_ms ? $log->processing_time_ms . 'ms' : 'N/A',
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            }
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $logs->total(),
            'recordsFiltered' => $logs->total(),
            'data' => $data,
        ]);
    }
}

