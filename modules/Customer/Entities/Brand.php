<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Localization\Traits\Translatable;
use Modules\Tenant\Entities\Tenant;
use Modules\Auth\Entities\User;
use OwenIt\Auditing\Contracts\Auditable;

class Brand extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Translatable, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'logo',
        'name',
        'slug',
        'description',
        'website',
        'email',
        'phone',
        'address',
        'status',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $translatable = [
        'name',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
            if (auth()->check()) {
                $brand->created_by = auth()->id();
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name') && empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
            if (auth()->check()) {
                $brand->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get the tenant that owns the brand.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who created the brand.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the brand.
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
     * Scope for searching brands.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('slug', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get the brand's logo URL.
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    /**
     * Get the brand's module subscriptions.
     */
    public function moduleSubscriptions()
    {
        return $this->hasMany(BrandModuleSubscription::class);
    }

    /**
     * Get active module subscriptions.
     */
    public function activeModuleSubscriptions()
    {
        return $this->hasMany(BrandModuleSubscription::class)->validSubscription();
    }

    /**
     * Get modules accessible by this brand.
     */
    public function getAccessibleModules()
    {
        return $this->activeModuleSubscriptions()
                   ->select('module_key', 'module_name')
                   ->get()
                   ->pluck('module_name', 'module_key');
    }

    /**
     * Check if brand has access to a specific module.
     */
    public function hasModuleAccess($moduleKey)
    {
        return $this->moduleSubscriptions()
                   ->validSubscription()
                   ->where('module_key', $moduleKey)
                   ->exists();
    }

    /**
     * Get the brand's branches.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get active branches.
     */
    public function activeBranches()
    {
        return $this->hasMany(Branch::class)->where('status', 'active');
    }

    /**
     * Get branches count.
     */
    public function getBranchesCountAttribute()
    {
        return $this->branches()->count();
    }

    /**
     * Get active branches count.
     */
    public function getActiveBranchesCountAttribute()
    {
        return $this->activeBranches()->count();
    }
}
