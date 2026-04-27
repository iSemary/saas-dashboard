<?php

namespace Modules\Customer\Repository;

use Modules\Customer\Entities\BrandModuleSubscription;
use Modules\Customer\Repository\BrandModuleSubscriptionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BrandModuleSubscriptionRepository implements BrandModuleSubscriptionRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BrandModuleSubscription::with(['brand', 'creator', 'updater']);

        if (isset($filters['brand_id']))
        {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['module_key']))
        {
            $query->where('module_key', $filters['module_key']);
        }

        if (isset($filters['status']))
        {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search']))
        {
            $query->where(function ($q) use ($filters)
            {
                $q->whereHas('brand', function ($brandQuery) use ($filters)
                {
                    $brandQuery->where('name', 'like', '%' . $filters['search'] . '%');
                })
                ->orWhere('module_key', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['date_from']))
        {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']))
        {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getById(int $id): ?BrandModuleSubscription
    {
        return BrandModuleSubscription::withTrashed()->with(['brand', 'creator', 'updater'])->find($id);
    }

    public function getByBrandAndModule(int $brandId, string $moduleKey): ?BrandModuleSubscription
    {
        return BrandModuleSubscription::withTrashed()
                                   ->where('brand_id', $brandId)
                                   ->where('module_key', $moduleKey)
                                   ->first();
    }

    public function create(array $data): BrandModuleSubscription
    {
        return BrandModuleSubscription::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $subscription = $this->getById($id);
        if (!$subscription)
        {
            return false;
        }

        return $subscription->update($data);
    }

    public function delete(int $id): bool
    {
        $subscription = $this->getById($id);
        if (!$subscription)
        {
            return false;
        }

        return $subscription->delete();
    }

    public function restore(int $id): bool
    {
        $subscription = BrandModuleSubscription::withTrashed()->find($id);
        if (!$subscription)
        {
            return false;
        }

        return $subscription->restore();
    }

    public function getByBrand(int $brandId): Collection
    {
        return BrandModuleSubscription::where('brand_id', $brandId)
                                   ->with(['brand'])
                                   ->get();
    }

    public function getByModuleKey(string $moduleKey): Collection
    {
        return BrandModuleSubscription::where('module_key', $moduleKey)
                                   ->with(['brand'])
                                   ->get();
    }

    public function getActiveSubscriptions(int $brandId): Collection
    {
        return BrandModuleSubscription::where('brand_id', $brandId)
                                   ->validSubscription()
                                   ->with(['brand'])
                                   ->get();
    }

    public function toggleSubscriptionStatus(int $id): bool
    {
        $subscription = $this->getById($id);
        if (!$subscription)
        {
            return false;
        }

        $newStatus = $subscription->status === 'active' ? 'inactive' : 'active';

        return $subscription->update(['status' => $newStatus]);
    }

    public function hasActiveSubscription(int $brandId, string $moduleKey): bool
    {
        return BrandModuleSubscription::where('brand_id', $brandId)
                                   ->where('module_key', $moduleKey)
                                   ->validSubscription()
                                   ->exists();
    }

    public function getDashboardStats(): array
    {
        return [
            'total_subscriptions' => BrandModuleSubscription::count(),
            'active_subscriptions' => BrandModuleSubscription::active()->count(),
            'inactive_subscriptions' => BrandModuleSubscription::where('status', 'inactive')->count(),
            'suspended_subscriptions' => BrandModuleSubscription::where('status', 'suspended')->count(),
            'expired_subscriptions' => BrandModuleSubscription::where('status', 'expired')->count(),
            'subscriptions_by_module' => BrandModuleSubscription::select('module_key', DB::raw('count(*) as count'))
                                           ->groupBy('module_key')
                                           ->get()
                                           ->pluck('count', 'module_key'),
            'subscriptions_by_brand' => BrandModuleSubscription::select('brand_id', DB::raw('count(*) as count'))
                                        ->groupBy('brand_id')
                                        ->get(),
        ];
    }

}
