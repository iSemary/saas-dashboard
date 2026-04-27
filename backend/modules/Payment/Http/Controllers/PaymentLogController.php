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
        $logType = $request->get('log_type', 'gateway');

        if ($logType === 'audit') {
            $query = PaymentAuditLog::with('user')->orderBy('created_at', 'desc');
        } else {
            $query = PaymentGatewayLog::with(['paymentMethod', 'transaction'])->orderBy('created_at', 'desc');
        }

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

        return $query->paginate($request->get('per_page', 15));
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

}

