<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Utilities\Entities\Currency;

class SubscriptionInvoiceItem extends Model
{
    use HasFactory;

    protected $connection = "landlord";

    protected $fillable = [
        'invoice_id',
        'line_type',
        'reference_type',
        'reference_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
        'tax_amount',
        'total_amount',
        'currency_id',
        'period_start',
        'period_end',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'period_start' => 'date',
        'period_end' => 'date',
        'metadata' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(SubscriptionInvoice::class, 'invoice_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
