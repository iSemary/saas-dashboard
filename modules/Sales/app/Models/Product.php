<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'cost',
        'stock_quantity',
        'min_stock_level',
        'category',
        'brand',
        'unit',
        'weight',
        'dimensions',
        'is_active',
        'is_digital',
        'images',
        'attributes',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_digital' => 'boolean',
        'images' => 'array',
        'attributes' => 'array',
        'custom_fields' => 'array',
    ];

    /**
     * Scope for active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for digital products.
     */
    public function scopeDigital($query)
    {
        return $query->where('is_digital', true);
    }

    /**
     * Scope for low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Check if product is low stock.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Get profit margin.
     */
    public function getProfitMarginAttribute(): float
    {
        if (!$this->cost || $this->cost == 0) {
            return 0;
        }

        return (($this->price - $this->cost) / $this->cost) * 100;
    }

    /**
     * Get profit amount.
     */
    public function getProfitAmountAttribute(): float
    {
        return $this->price - ($this->cost ?? 0);
    }
}
