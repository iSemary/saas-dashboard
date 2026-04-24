<?php

namespace Modules\Auth\Repositories\Tenant;

interface TenantSettingsRepositoryInterface
{
    public function all(): \Illuminate\Support\Collection;

    public function updateSettings(array $settings): void;
}
