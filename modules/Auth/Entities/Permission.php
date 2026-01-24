<?php

namespace Modules\Auth\Entities;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends SpatiePermission
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Get all permission groups that contain this permission.
     */
    public function permissionGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissionGroup::class,
            'permission_group_has_permissions',
            'permission_id',
            'permission_group_id'
        );
    }
}
