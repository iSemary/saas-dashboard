<?php

namespace Modules\Payment\Repositories;

use App\Helpers\TableHelper;
use Modules\Payment\Entities\PaymentTransaction;
use Yajra\DataTables\DataTables;

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

    public function datatables()
    {
        $rows = $this->model->query()->where(
            function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
                
                if (request()->status) {
                    $q->where('status', request()->status);
                }
                
                if (request()->payment_method_id) {
                    $q->where('payment_method_id', request()->payment_method_id);
                }
                
                if (request()->customer_id) {
                    $q->where('customer_id', request()->customer_id);
                }
            }
        )->with(['paymentMethod', 'currency']);

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . route('landlord.payment-transactions.show', $row->id) . '" class="btn btn-sm btn-outline-primary">View</a>';
                
                if ($row->canBeRefunded()) {
                    $actions .= '<a href="' . route('landlord.refunds.create', ['transaction' => $row->id]) . '" class="btn btn-sm btn-outline-warning">Refund</a>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->addColumn('status_badge', function ($row) {
                $statusColors = [
                    'pending' => 'warning',
                    'processing' => 'info',
                    'completed' => 'success',
                    'failed' => 'danger',
                    'cancelled' => 'secondary',
                    'refunded' => 'dark',
                    'partially_refunded' => 'warning',
                    'charged_back' => 'danger'
                ];
                $color = $statusColors[$row->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
            })
            ->addColumn('formatted_amount', function ($row) {
                return $row->formatted_amount;
            })
            ->addColumn('payment_method_name', function ($row) {
                return $row->paymentMethod->name ?? 'N/A';
            })
            ->addColumn('transaction_type_badge', function ($row) {
                $typeColors = [
                    'sale' => 'success',
                    'auth' => 'info',
                    'capture' => 'primary',
                    'refund' => 'warning',
                    'void' => 'secondary'
                ];
                $color = $typeColors[$row->transaction_type] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->transaction_type) . '</span>';
            })
            ->rawColumns(['actions', 'status_badge', 'transaction_type_badge'])
            ->make(true);
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
