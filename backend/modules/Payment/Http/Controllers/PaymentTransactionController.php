<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payment\Entities\PaymentTransaction;
use Modules\Payment\Repositories\PaymentTransactionInterface;
use Modules\Payment\Services\PaymentGatewayService;
use Modules\Payment\Entities\PaymentMethod;

class PaymentTransactionController extends Controller
{
    protected $repository;
    protected $paymentService;

    public function __construct(PaymentTransactionInterface $repository, PaymentGatewayService $paymentService)
    {
        $this->repository = $repository;
        $this->paymentService = $paymentService;

        $this->middleware('permission:view_payment_transactions')->only(['index', 'show']);
        $this->middleware('permission:manage_payment_transactions')->only(['capture', 'void', 'retry']);
    }

    /**
     * Display a listing of payment transactions.
     */
    public function index()
    {

        $stats = $this->repository->getAnalytics();
        $paymentMethods = PaymentMethod::active()->get();

        return view('payment::transactions.index', compact('stats', 'paymentMethods'));
    }

    /**
     * Display the specified transaction.
     */
    public function show($id)
    {
        $transaction = $this->repository->find($id);

        if (!$transaction) {
            abort(404);
        }

        return view('payment::transactions.show', compact('transaction'));
    }

    /**
     * Capture an authorized transaction.
     */
    public function capture(Request $request, $id)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $transaction = $this->repository->find($id);

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            if ($transaction->status !== 'authorized') {
                return response()->json(['error' => 'Transaction is not in authorized state'], 400);
            }

            $amount = $request->amount ?? $transaction->amount;
            $metadata = ['notes' => $request->notes];

            $response = $this->paymentService->capturePayment($transaction->transaction_id, $amount, $metadata);

            return response()->json([
                'success' => $response->isSuccess(),
                'message' => $response->isSuccess() ? 'Transaction captured successfully' : 'Capture failed',
                'data' => $response->toArray()
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Capture failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Void an authorized transaction.
     */
    public function void(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $transaction = $this->repository->find($id);

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            if ($transaction->status !== 'authorized') {
                return response()->json(['error' => 'Transaction is not in authorized state'], 400);
            }

            $metadata = ['reason' => $request->reason];

            $response = $this->paymentService->voidPayment($transaction->transaction_id, $metadata);

            return response()->json([
                'success' => $response->isSuccess(),
                'message' => $response->isSuccess() ? 'Transaction voided successfully' : 'Void failed',
                'data' => $response->toArray()
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Void failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get transaction analytics.
     */
    public function analytics(Request $request)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : now()->subDays(30);
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : now();

        $analytics = $this->repository->getAnalytics($startDate, $endDate);

        // Get daily transaction data for charts
        $dailyData = PaymentTransaction::selectRaw('DATE(created_at) as date,
                                                   COUNT(*) as transaction_count,
                                                   SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_amount,
                                                   SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as successful_count')
                                     ->whereBetween('created_at', [$startDate, $endDate])
                                     ->groupBy('date')
                                     ->orderBy('date')
                                     ->get();

        // Get payment method distribution
        $methodDistribution = PaymentTransaction::selectRaw('payment_method_id,
                                                           COUNT(*) as transaction_count,
                                                           SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_amount')
                                               ->whereBetween('created_at', [$startDate, $endDate])
                                               ->with('paymentMethod')
                                               ->groupBy('payment_method_id')
                                               ->get();

        return response()->json([
            'analytics' => $analytics,
            'daily_data' => $dailyData,
            'method_distribution' => $methodDistribution,
        ]);
    }

    /**
     * Export transactions.
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        // This would typically use a job for large exports
        $transactions = PaymentTransaction::query()
                                        ->when($request->start_date, function ($query) use ($request) {
                                            $query->whereDate('created_at', '>=', $request->start_date);
                                        })
                                        ->when($request->end_date, function ($query) use ($request) {
                                            $query->whereDate('created_at', '<=', $request->end_date);
                                        })
                                        ->when($request->status, function ($query) use ($request) {
                                            $query->where('status', $request->status);
                                        })
                                        ->when($request->payment_method_id, function ($query) use ($request) {
                                            $query->where('payment_method_id', $request->payment_method_id);
                                        })
                                        ->with(['paymentMethod', 'currency'])
                                        ->get();

        // Return download response based on format
        switch ($request->format) {
            case 'csv':
                return $this->exportToCsv($transactions);
            case 'xlsx':
                return $this->exportToExcel($transactions);
            case 'pdf':
                return $this->exportToPdf($transactions);
        }
    }

    /**
     * Export transactions to CSV.
     */
    protected function exportToCsv($transactions)
    {
        $filename = 'transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Transaction ID', 'Gateway Transaction ID', 'Payment Method', 'Amount', 'Currency',
                'Status', 'Type', 'Customer ID', 'Created At', 'Processed At'
            ]);

            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_id,
                    $transaction->gateway_transaction_id,
                    $transaction->paymentMethod->name ?? 'N/A',
                    $transaction->amount,
                    $transaction->currency->code ?? 'N/A',
                    $transaction->status,
                    $transaction->transaction_type,
                    $transaction->customer_id,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->processed_at ? $transaction->processed_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export transactions to Excel (placeholder).
     */
    protected function exportToExcel($transactions)
    {
        // This would use a package like Laravel Excel
        return response()->json(['message' => translate('message.operation_failed')], 501);
    }

    /**
     * Export transactions to PDF (placeholder).
     */
    protected function exportToPdf($transactions)
    {
        // This would use a package like DomPDF or wkhtmltopdf
        return response()->json(['message' => translate('message.operation_failed')], 501);
    }
}
