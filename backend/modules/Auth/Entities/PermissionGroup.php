<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PermissionGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'guard_name',
        'description',
    ];

    protected $dates = ['deleted_at'];

    protected static function booted(): void
    {
        static::creating(function (PermissionGroup $group): void {
            if (empty($group->slug) && filled($group->name)) {
                $group->slug = static::uniqueSlug(Str::slug($group->name));
            }
        });
    }

    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base !== '' ? $base : 'group';
        $i = 1;
        while (static::query()
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

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
