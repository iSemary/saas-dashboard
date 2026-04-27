<?php

namespace Modules\Subscription\Repositories;

use App\Helpers\TableHelper;
use Modules\Subscription\Entities\Plan;
class PlanRepository implements PlanInterface
{
    protected $model;

    public function __construct(Plan $plan)
    {
        $this->model = $plan;
    }

    public function all()
    {
        return $this->model->with(['features', 'prices', 'trials'])->ordered()->get();
    }

    public function find($id)
    {
        return $this->model->with(['features', 'prices.currency', 'trials', 'discounts'])->find($id);
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
        $data['status'] = $data['status'] ?? 'active';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_popular'] = isset($data['is_popular']) && $data['is_popular'] ? true : false;
        $data['is_custom'] = isset($data['is_custom']) && $data['is_custom'] ? true : false;

        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $plan = $this->find($id);
        
        if (!$plan) {
            return false;
        }

        $data['is_popular'] = isset($data['is_popular']) && $data['is_popular'] ? true : false;
        $data['is_custom'] = isset($data['is_custom']) && $data['is_custom'] ? true : false;

        if (isset($data['name']) && (empty($data['slug']) || $data['slug'] !== $plan->slug)) {
            $data['slug'] = \Str::slug($data['name']);
        }

        return $plan->update($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)?->delete();
    }

    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)?->restore();
    }

    public function getActive()
    {
        return $this->model->active()->ordered()->get();
    }

    public function getPopular()
    {
        return $this->model->active()->popular()->ordered()->get();
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->active()->first();
    }

    public function getWithPricing($currencyCode = 'USD', $countryCode = null)
    {
        return $this->model->active()
                          ->with(['features' => function ($query) {
                              $query->active()->ordered();
                          }])
                          ->with(['prices' => function ($query) use ($currencyCode, $countryCode) {
                              $query->active()
                                   ->valid()
                                   ->whereHas('currency', function ($q) use ($currencyCode) {
                                       $q->where('code', $currencyCode);
                                   })
                                   ->where(function ($q) use ($countryCode) {
                                       $q->where('country_code', $countryCode)
                                         ->orWhereNull('country_code');
                                   });
                          }])
                          ->with(['trials' => function ($query) use ($countryCode) {
                              $query->active()
                                   ->where(function ($q) use ($countryCode) {
                                       $q->where('country_code', $countryCode)
                                         ->orWhereNull('country_code');
                                   });
                          }])
                          ->ordered()
                          ->get();
    }

    public function getAvailableUpgrades($planId)
    {
        $plan = $this->find($planId);
        if (!$plan) {
            return collect();
        }

        return $plan->getAllowedChanges('upgrade');
    }

    public function getAvailableDowngrades($planId)
    {
        $plan = $this->find($planId);
        if (!$plan) {
            return collect();
        }

        return $plan->getAllowedChanges('downgrade');
    }
}
