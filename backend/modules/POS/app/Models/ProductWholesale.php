<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class ProductWholesale extends Model
{
    protected $table = 'pos_product_wholesales';

    protected $fillable = [
        'parent_id',
        'child_id',
        'quantity',
        'created_by',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'child_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
