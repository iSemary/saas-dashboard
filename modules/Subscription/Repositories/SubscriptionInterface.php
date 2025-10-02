<?php

namespace Modules\Subscription\Repositories;

interface SubscriptionInterface
{
    public function all();
    public function datatables();
    public function find($id);
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
