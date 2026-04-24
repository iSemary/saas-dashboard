<?php

namespace Modules\Auth\Repositories\Tenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TenantSettingsRepository implements TenantSettingsRepositoryInterface
{
    public function all(): Collection
    {
        return DB::table('tenant_settings')->get()->pluck('value', 'key');
    }

    public function updateSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            DB::table('tenant_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value, 'updated_at' => now()]
            );
        }
    }
}
