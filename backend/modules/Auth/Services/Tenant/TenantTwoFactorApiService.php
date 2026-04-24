<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Repositories\Tenant\TenantTwoFactorApiRepositoryInterface;

class TenantTwoFactorApiService
{
    public function __construct(protected TenantTwoFactorApiRepositoryInterface $repository) {}

    public function setup(int $userId): array
    {
        $google2fa = app('pragmarx.google2fa');
        $secret = $google2fa->generateSecretKey();
        $user = \App\Models\User::find($userId);

        $this->repository->updateTwoFactorSecret($userId, $secret);

        return [
            'secret' => $secret,
            'qr_code' => $google2fa->getQRCodeInline(config('app.name'), $user->email, $secret),
        ];
    }

    public function confirm(int $userId, string $code): array
    {
        $user = \App\Models\User::find($userId);
        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($user->two_factor_secret, $code);

        if (!$valid) {
            return ['error' => 'Invalid 2FA code', 'code' => 422];
        }

        $this->repository->enableTwoFactor($userId);
        return ['message' => '2FA enabled successfully'];
    }

    public function disable(int $userId): void
    {
        $this->repository->disableTwoFactor($userId);
    }
}
