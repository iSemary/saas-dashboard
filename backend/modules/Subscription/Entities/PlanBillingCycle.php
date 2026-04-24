<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Utilities\Entities\Currency;

class PlanBillingCycle extends Model
{
    use HasFactory;

    protected $connection = "landlord";

    protected $fillable = [
        'plan_id', 'currency_id', 'country_code', 'billing_cycle', 'billing_interval',
        'custom_period', 'price', 'setup_fee', 'discount_percentage', 'is_default',
        'is_popular', 'sort_order', 'description', 'metadata', 'status'
    ];

    protected $casts = [
        'billing_interval' => 'integer', 'price' => 'decimal:2', 'setup_fee' => 'decimal:2',
        'discount_percentage' => 'decimal:2', 'is_default' => 'boolean', 'is_popular' => 'boolean',
        'sort_order' => 'integer', 'metadata' => 'array',
    ];

    public function plan() { return $this->belongsTo(Plan::class); }
    public function currency() { return $this->belongsTo(Currency::class); }
}
