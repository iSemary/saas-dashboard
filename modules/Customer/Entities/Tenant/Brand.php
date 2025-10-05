<?php

namespace Modules\Customer\Entities\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Localization\Traits\Translatable;
use Modules\Auth\Entities\User;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Auth;

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
            if (Auth::check()) {
                $brand->created_by = Auth::id();
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name') && empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
            if (Auth::check()) {
                $brand->updated_by = Auth::id();
            }
        });
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
     * Get the brand's branches.
     */
    public function branches()
    {
        return $this->hasMany(\Modules\Customer\Entities\Branch::class);
    }

    /**
     * Get active branches.
     */
    public function activeBranches()
    {
        return $this->hasMany(\Modules\Customer\Entities\Branch::class)->where('status', 'active');
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
