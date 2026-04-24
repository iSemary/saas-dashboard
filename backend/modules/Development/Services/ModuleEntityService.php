<?php

namespace Modules\Development\Services;

use Modules\Utilities\Entities\Entity;
use Modules\Utilities\Entities\ModuleEntity;

class ModuleEntityService
{
    public function list()
    {
        return Entity::orderBy('entity_name')->get();
    }

    public function getMap(): array
    {
        $map = [];
        $rows = ModuleEntity::all();
        foreach ($rows as $row) {
            if (!isset($map[$row->module_id])) {
                $map[$row->module_id] = [];
            }
            $map[$row->module_id][] = $row->entity_id;
        }
        return $map;
    }

    public function sync(array $entities): void
    {
        ModuleEntity::truncate();
        foreach ($entities as $moduleId => $entityIds) {
            foreach ($entityIds as $entityId) {
                ModuleEntity::create([
                    'module_id' => $moduleId,
                    'entity_id' => $entityId,
                ]);
            }
        }
    }
}
