<?php

namespace Modules\Auth\Repositories\Tenant;

use App\Models\User;

class TenantTwoFactorApiRepository implements TenantTwoFactorApiRepositoryInterface
{
    public function updateTwoFactorSecret(int $userId, string $secret): bool
    {
        return User::where('id', $userId)->update(['two_factor_secret' => $secret]);
    }

    public function enableTwoFactor(int $userId): bool
    {
        return User::where('id', $userId)->update(['two_factor_enabled' => true]);
    }

    public function disableTwoFactor(int $userId): bool
    {
        return User::where('id', $userId)->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);
    }
}
