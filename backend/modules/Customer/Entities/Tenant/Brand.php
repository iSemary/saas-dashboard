<?php

namespace Modules\Customer\Entities\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Localization\Traits\Translatable;
use Modules\Auth\Entities\User;
use Modules\Utilities\Entities\Module;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Auth;

class Brand extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Translatable, \OwenIt\Auditing\Auditable;

    protected $connection = 'tenant';

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

        static::creating(function ($brand) 
        {
            if (empty($brand->slug)) 
            {
                $brand->slug = Str::slug($brand->name);
            }
            if (Auth::check()) 
            {
                $brand->created_by = Auth::id();
            }
        });

        static::updating(function ($brand) 
        {
            if ($brand->isDirty('name') && empty($brand->slug)) 
            {
                $brand->slug = Str::slug($brand->name);
            }
            if (Auth::check()) 
            {
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
     * Get the modules assigned to this brand.
     */
    public function modules()
    {
        // Since modules are in landlord DB and brands are in tenant DB,
        // we need to handle this relationship manually
        return collect();
    }

    /**
     * Get modules count for this brand.
     */
    public function getModulesCountAttribute()
    {
        return \DB::table('brand_module')
            ->where('brand_id', $this->id)
            ->count();
    }

    /**
     * Get modules assigned to this brand using direct landlord database access
     */
    public function getAssignedModules()
    {
        try {
            // Get module IDs from pivot table
            $moduleIds = \DB::table('brand_module')
                ->where('brand_id', $this->id)
                ->pluck('module_id')
                ->toArray();
            
            if (empty($moduleIds)) {
                return collect();
            }
            
            // Get modules from landlord database directly
            $modules = \DB::connection('landlord')
                ->table('modules')
                ->whereIn('id', $moduleIds)
                ->select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                ->get();
            
            return $modules;
            
        } catch (\Exception $e) {
            \Log::error('Brand getAssignedModules Error: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Scope for searching brands.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) 
        {
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
        if ($this->logo) 
        {
            return asset('storage/' . $this->logo);
        }
        return asset('assets/shared/images/placeholder-brand.png');
    }

    /**
     * Check if brand has access to a specific module.
     */
    public function hasModuleAccess($moduleKey)
    {
        return $this->modules()
                   ->where('module_key', $moduleKey)
                   ->exists();
    }

    /**
     * Get the brand's dashboard route.
     */
    public function getDashboardRoute()
    {
        return route('brand.dashboard', ['brand' => $this->slug]);
    }
}