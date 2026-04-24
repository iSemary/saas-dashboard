<?php

namespace Modules\Customer\Entities\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\Utilities\Entities\Module;
use OwenIt\Auditing\Contracts\Auditable;

class BrandModule extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'brand_id',
        'module_id',
        'module_key',
        'status',
        'color_palette',
        'module_config',
        'subscribed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'color_palette' => 'array',
        'module_config' => 'array',
        'subscribed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
            if (empty($model->subscribed_at)) {
                $model->subscribed_at = now();
            }
            // Auto-populate module_key from landlord modules table
            if (empty($model->module_key) && $model->module_id) {
                $landlordModule = Module::on('landlord')->find($model->module_id);
                if ($landlordModule) {
                    $model->module_key = $landlordModule->module_key;
                }
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the landlord module details (cross-database).
     */
    public function landlordModule()
    {
        return Module::on('landlord')->where('id', $this->module_id)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
