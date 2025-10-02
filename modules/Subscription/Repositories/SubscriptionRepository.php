<?php

namespace Modules\Subscription\Repositories;

use App\Helpers\TableHelper;
use Modules\Subscription\Entities\PlanSubscription;
use Yajra\DataTables\DataTables;

class SubscriptionRepository implements SubscriptionInterface
{
    protected $model;

    public function __construct(PlanSubscription $subscription)
    {
        $this->model = $subscription;
    }

    public function all()
    {
        return $this->model->with(['user', 'plan', 'currency'])->latest()->get();
    }

    public function datatables()
    {
        $rows = $this->model->query()->where(
            function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            }
        )->with(['brand', 'user', 'plan', 'currency']);

        return DataTables::of($rows)
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.subscriptions.edit',
                    deleteRoute: 'landlord.subscriptions.destroy',
                    restoreRoute: null,
                    type: 'subscriptions',
                    titleType: 'subscription',
                    showIconsOnly: false
                );
            })
            ->addColumn('brand_name', function ($row) {
                return $row->brand ? $row->brand->name : 'N/A';
            })
            ->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : 'N/A';
            })
            ->addColumn('plan_name', function ($row) {
                return $row->plan ? $row->plan->name : 'N/A';
            })
            ->addColumn('status', function ($row) {
                return '<span class="badge bg-' . $row->status_color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('formatted_price', function ($row) {
                return $row->formatted_price;
            })
            ->addColumn('next_billing', function ($row) {
                return $row->next_billing_at ? $row->next_billing_at->format('Y-m-d') : 'N/A';
            })
            ->addColumn('days_remaining', function ($row) {
                $days = $row->getDaysRemaining();
                return $days !== null ? $days . ' days' : 'N/A';
            })
            ->rawColumns(['actions', 'status'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->with(['user', 'plan', 'currency', 'invoices', 'payments'])->find($id);
    }

    public function create(array $data)
    {
        $data['subscription_id'] = $data['subscription_id'] ?? 'sub_' . uniqid() . '_' . time();
        $data['status'] = $data['status'] ?? 'trial';
        $data['auto_renew'] = $data['auto_renew'] ?? 'enabled';
        $data['user_count'] = $data['user_count'] ?? 1;

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $subscription = $this->find($id);
        
        if (!$subscription) {
            return false;
        }

        return $subscription->update($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)?->delete();
    }

    public function getByUser($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->with(['plan', 'currency'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getActiveByUser($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->whereIn('status', ['trial', 'active'])
                          ->with(['plan', 'currency'])
                          ->first();
    }

    public function getDueForBilling()
    {
        return $this->model->dueForBilling()
                          ->with(['user', 'plan', 'currency'])
                          ->get();
    }

    public function getExpiringSoon($days = 7)
    {
        return $this->model->endingSoon($days)
                          ->with(['user', 'plan', 'currency'])
                          ->get();
    }

    public function getByStatus($status)
    {
        return $this->model->where('status', $status)
                          ->with(['user', 'plan', 'currency'])
                          ->get();
    }

    public function getTrialSubscriptions()
    {
        return $this->model->trial()
                          ->with(['user', 'plan', 'currency'])
                          ->get();
    }
}
