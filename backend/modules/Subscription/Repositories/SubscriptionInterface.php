<?php

namespace Modules\Subscription\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SubscriptionInterface
{
    public function all();
    public function datatables();
    public function find($id);
    public function findOrFail(int $id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByUser($userId);
    public function getActiveByUser($userId);
    public function getDueForBilling();
    public function getExpiringSoon($days = 7);
    public function getByStatus($status);
    public function getTrialSubscriptions();
}
