<?php

namespace Modules\Subscription\Repositories;

use App\Helpers\TableHelper;
use Modules\Subscription\Entities\PlanSubscription;
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

    public function find($id)
    {
        return $this->model->with(['user', 'plan', 'currency', 'invoices', 'payments'])->find($id);
    }

    public function findOrFail(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model->query();
        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
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

    public function restore($id)
    {
        $subscription = $this->model->withTrashed()->find($id);
        return $subscription ? $subscription->restore() : false;
    }
}
