<?php

namespace Modules\Sales\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderInstallment extends Model
{
    protected $table = 'sales_order_installments';

    protected $fillable = [
        'order_id', 'installment_type', 'total_months', 'paid_months', 'monthly_amount',
    ];

    protected $casts = ['monthly_amount' => 'decimal:2'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function getRemainingMonths(): int
    {
        return max(0, $this->total_months - $this->paid_months);
    }

    public function getRemainingAmount(): float
    {
        return $this->getRemainingMonths() * (float) $this->monthly_amount;
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_months >= $this->total_months;
    }
}
