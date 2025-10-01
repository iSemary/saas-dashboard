<?php

namespace Modules\Tenant\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\Tenant\Entities\Tenant;
use OwenIt\Auditing\Contracts\Auditable;

class TenantOwner extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role',
        'is_super_admin',
        'permissions',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_super_admin' => 'boolean',
        'permissions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenantOwner) {
            if (auth()->check()) {
                $tenantOwner->created_by = auth()->id();
            }
        });

        static::updating(function ($tenantOwner) {
            if (auth()->check()) {
                $tenantOwner->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get the tenant that owns this tenant owner.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user associated with this tenant owner.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created this tenant owner.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this tenant owner.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for filtering by tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope for filtering by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for filtering super admins.
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where('is_super_admin', true);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for searching tenant owners.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('username', 'like', '%' . $search . '%');
        })->orWhere('role', 'like', '%' . $search . '%');
    }

    /**
     * Check if the tenant owner has a specific permission.
     */
    public function hasPermission($permission)
    {
        if ($this->is_super_admin) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Get the tenant owner's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown';
    }

    /**
     * Get the tenant owner's email.
     */
    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : 'Unknown';
    }

    /**
     * Get the tenant owner's username.
     */
    public function getUsernameAttribute()
    {
        return $this->user ? $this->user->username : 'Unknown';
    }
}
