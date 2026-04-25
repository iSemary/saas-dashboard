<?php

namespace Modules\POS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class OfferPrice extends Model
{
    use SoftDeletes;

    protected $table = 'pos_offer_prices';

    protected $fillable = [
        'product_id',
        'branch_id',
        'amount',
        'original_price',
        'total_price',
        'buyer_name',
        'reduce_stock',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'original_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'reduce_stock' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
