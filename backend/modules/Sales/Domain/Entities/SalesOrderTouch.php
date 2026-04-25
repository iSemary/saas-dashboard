<?php

namespace Modules\Sales\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderTouch extends Model
{
    protected $table = 'sales_order_touches';

    protected $fillable = ['order_id', 'order_type', 'table_number', 'service_fee'];

    protected $casts = ['service_fee' => 'decimal:2'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }
}
