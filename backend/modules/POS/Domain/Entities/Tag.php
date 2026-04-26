<?php

namespace Modules\POS\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class Tag extends Model
{
    protected $table = 'pos_tags';

    protected $fillable = ['type', 'value', 'created_by', 'brand_id'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'pos_product_tags', 'tag_id', 'product_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Entities\Tenant\Brand::class);
    }

    public function scopeForBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }
}
