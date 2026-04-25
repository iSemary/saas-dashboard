<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class ProductStock extends Model
{
    use SoftDeletes;

    protected $table = 'pos_product_stocks';

    protected $fillable = [
        'product_id',
        'branch_id',
        'tag_id',
        'quantity',
        'object_id',
        'model',
        'main_price',
        'total_price',
        'currency_id',
        'barcode',
        'created_by',
    ];

    protected $casts = [
        'tag_id' => 'array',
        'main_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeByModel($query, string $model)
    {
        return $query->where('model', $model);
    }
}
