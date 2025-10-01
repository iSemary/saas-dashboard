<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'is_active',
        'is_default',
        'latitude',
        'longitude',
        'manager_id',
        'created_by',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'custom_fields' => 'array',
    ];

    /**
     * Get the manager of this warehouse.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the user who created this warehouse.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the stock moves for this warehouse.
     */
    public function stockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class);
    }

    /**
     * Get the stock valuations for this warehouse.
     */
    public function stockValuations(): HasMany
    {
        return $this->hasMany(StockValuation::class);
    }

    /**
     * Get the reorder rules for this warehouse.
     */
    public function reorderRules(): HasMany
    {
        return $this->hasMany(ReorderRule::class);
    }

    /**
     * Scope for active warehouses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for default warehouse.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get the default warehouse.
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Set as default warehouse.
     */
    public function setAsDefault()
    {
        // Remove default from other warehouses
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }
}
