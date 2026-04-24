<?php

namespace Modules\Development\Services;

use Illuminate\Support\Facades\DB;
use Modules\Development\DTOs\CreateFeatureFlagData;
use Modules\Development\DTOs\UpdateFeatureFlagData;

class FeatureFlagService
{
    public function list(int $perPage = 50)
    {
        return DB::table('feature_flags')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(CreateFeatureFlagData $data): array
    {
        $arrayData = [
            'name' => $data->name,
            'slug' => $data->slug,
            'description' => $data->description,
            'is_enabled' => $data->is_enabled,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('feature_flags')->insert($arrayData);
        return $arrayData;
    }

    public function update($id, UpdateFeatureFlagData $data): array
    {
        $arrayData = array_merge($data->toArray(), ['updated_at' => now()]);
        DB::table('feature_flags')->where('id', $id)->update($arrayData);
        return $arrayData;
    }

    public function delete($id)
    {
        return DB::table('feature_flags')->where('id', $id)->delete();
    }
}
