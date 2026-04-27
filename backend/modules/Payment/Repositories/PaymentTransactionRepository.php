<?php

namespace Modules\Payment\Repositories;

use App\Helpers\TableHelper;
use Modules\Payment\Entities\PaymentTransaction;
class PaymentTransactionRepository implements PaymentTransactionInterface
{
    protected $model;

    public function __construct(PaymentTransaction $paymentTransaction)
    {
        $this->model = $paymentTransaction;
    }

    public function all()
    {
        return $this->model->with(['paymentMethod', 'currency', 'refunds', 'chargebacks'])->get();
    }

    public function find($id)
    {
        return $this->model->with(['paymentMethod', 'currency', 'refunds', 'chargebacks', 'gatewayLogs'])->find($id);
    }

    public function getByCustomer($customerId)
    {
        return $this->model->byCustomer($customerId)
                          ->with(['paymentMethod', 'currency'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getByStatus($status)
    {
        return $this->model->byStatus($status)
                          ->with(['paymentMethod', 'currency'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->model->byDateRange($startDate, $endDate)
                          ->with(['paymentMethod', 'currency'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getSuccessful()
    {
        return $this->model->successful()
                          ->with(['paymentMethod', 'currency'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getFailed()
    {
        return $this->model->failed()
                          ->with(['paymentMethod', 'currency'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getAnalytics($startDate = null, $endDate = null)
    {
        $query = $this->model->query();
        
        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }

        $transactions = $query->get();

        return [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->where('status', 'completed')->count(),
            'failed_transactions' => $transactions->where('status', 'failed')->count(),
            'total_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'total_fees' => $transactions->where('status', 'completed')->sum('total_fees'),
            'success_rate' => $transactions->count() > 0 
                ? round(($transactions->where('status', 'completed')->count() / $transactions->count()) * 100, 2)
                : 0,
            'average_amount' => $transactions->where('status', 'completed')->avg('amount') ?? 0,
            'refunded_amount' => $transactions->where('status', 'refunded')->sum('amount'),
            'chargeback_amount' => $transactions->where('status', 'charged_back')->sum('amount'),
        ];
    }
}
