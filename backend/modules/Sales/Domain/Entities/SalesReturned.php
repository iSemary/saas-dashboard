<?php

namespace Modules\Sales\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class SalesReturned extends Model
{
    use SoftDeletes;

    protected $table = 'sales_returneds';

    protected $fillable = [
        'user_id', 'branch_id', 'products', 'barcode', 'total_price',
        'amount_paid', 'tax', 'pay_method', 'returned_at',
    ];

    protected $casts = [
        'products'    => 'array',
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'tax'         => 'decimal:2',
        'returned_at' => 'datetime',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
