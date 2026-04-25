<?php

namespace Modules\Sales\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesDelivery extends Model
{
    protected $table = 'sales_deliveries';

    protected $fillable = [
        'order_id', 'full_name', 'phone_number', 'address', 'delivery_man', 'delivery_fee',
    ];

    protected $casts = ['delivery_fee' => 'decimal:2'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }
}
