<?php

namespace Modules\Auth\Services\Tenant;

use Modules\Auth\Repositories\Tenant\TenantSettingsRepositoryInterface;

class TenantSettingsService
{
    public function __construct(protected TenantSettingsRepositoryInterface $repository) {}

    public function all()
    {
        return $this->repository->all();
    }

    public function update(array $settings): void
    {
        $this->repository->updateSettings($settings);
    }
}
