<?php

namespace App\Repositories\CrossDb;

use Modules\Utilities\Entities\Module;

class LandlordRepository implements LandlordRepositoryInterface
{
    public function getModules(array $filters = []): \Illuminate\Support\Collection
    {
        $query = Module::query();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['module_key'])) {
            $query->where('module_key', $filters['module_key']);
        }

        return $query->select(['id', 'module_key', 'name', 'description', 'theme', 'status'])
                     ->orderBy('name')
                     ->get()
                     ->map(function ($module) {
                         return (object)[
                             'id' => $module->id,
                             'module_key' => $module->module_key,
                             'name' => $module->name,
                             'description' => $module->description,
                             'theme' => $module->theme,
                             'status' => $module->status,
                         ];
                     });
    }

    public function findModule(int $id): ?Module
    {
        return Module::select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                     ->find($id);
    }

    public function getModulesByIds(array $ids): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($ids)) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return Module::whereIn('id', $ids)
                     ->select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                     ->get();
    }

    public function getModuleStats(): array
    {
        return [
            'total_modules' => Module::count(),
            'active_modules' => Module::where('status', 'active')->count(),
            'inactive_modules' => Module::where('status', 'inactive')->count(),
        ];
    }
}
