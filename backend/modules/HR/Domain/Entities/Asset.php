<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Customer\Entities\Tenant\Brand;

class Asset extends Model
{
    use SoftDeletes;

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeForBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    protected $table = 'hr_assets';

    protected $fillable = [
        'asset_tag',
        'category_id',
        'name',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'purchase_cost',
        'status',
        'brand_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
    ];
}
