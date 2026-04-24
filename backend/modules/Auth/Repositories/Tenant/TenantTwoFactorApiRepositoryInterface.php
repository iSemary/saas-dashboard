<?php

namespace Modules\Auth\Repositories\Tenant;

use App\Models\User;

interface TenantTwoFactorApiRepositoryInterface
{
    public function updateTwoFactorSecret(int $userId, string $secret): bool;

    public function enableTwoFactor(int $userId): bool;

    public function disableTwoFactor(int $userId): bool;
}
