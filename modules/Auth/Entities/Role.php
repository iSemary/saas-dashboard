<?php

namespace Modules\Auth\Entities;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * Get all permission groups assigned to this role.
     */
    public function permissionGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissionGroup::class,
            'role_has_permission_groups',
            'role_id',
            'permission_group_id'
        );
    }
}
