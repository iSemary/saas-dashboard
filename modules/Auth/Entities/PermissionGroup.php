<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'guard_name',
        'description',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get all permissions in this group.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_group_has_permissions',
            'permission_group_id',
            'permission_id'
        );
    }

    /**
     * Get all roles that have this permission group.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_has_permission_groups',
            'permission_group_id',
            'role_id'
        );
    }
}
