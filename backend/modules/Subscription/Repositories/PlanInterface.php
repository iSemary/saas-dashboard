<?php

namespace Modules\Subscription\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PlanInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function findOrFail(int $id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function getActive();
    public function getPopular();
    public function getBySlug($slug);
    public function getWithPricing($currencyCode = 'USD', $countryCode = null);
    public function getAvailableUpgrades($planId);
    public function getAvailableDowngrades($planId);
}
