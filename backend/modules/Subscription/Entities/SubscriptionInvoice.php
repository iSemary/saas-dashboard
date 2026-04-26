<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Utilities\Entities\Currency;
use Modules\Customer\Entities\Brand;
use App\Models\User;

class SubscriptionInvoice extends Model
{
    use HasFactory;

    protected $connection = "landlord";

    protected $fillable = [
        'invoice_number', 'brand_id', 'subscription_id', 'user_id', 'plan_id', 'currency_id',
        'country_code', 'invoice_type', 'subtotal', 'discount_amount', 'tax_amount',
        'total_amount', 'line_items', 'applied_discounts', 'tax_breakdown',
        'invoice_date', 'due_date', 'period_start', 'period_end', 'paid_at',
        'voided_at', 'notes', 'external_invoice_id', 'billing_address',
        'metadata', 'status'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2', 'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2', 'line_items' => 'array', 'applied_discounts' => 'array',
        'tax_breakdown' => 'array', 'invoice_date' => 'datetime', 'due_date' => 'datetime',
        'period_start' => 'datetime', 'period_end' => 'datetime', 'paid_at' => 'datetime',
        'voided_at' => 'datetime', 'billing_address' => 'array', 'metadata' => 'array',
    ];

    public function brand() { return $this->belongsTo(Brand::class); }
    public function subscription() { return $this->belongsTo(PlanSubscription::class, 'subscription_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
    public function currency() { return $this->belongsTo(Currency::class); }
    public function payments() { return $this->hasMany(SubscriptionPayment::class, 'invoice_id'); }
    public function items() { return $this->hasMany(SubscriptionInvoiceItem::class, 'invoice_id'); }
}
