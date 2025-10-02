<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Utilities\Entities\Currency;

class PlanPriceByUser extends Model
{
    use HasFactory;

    protected $connection = "landlord";
    protected $table = 'plan_prices_by_users';

    protected $fillable = [
        'plan_id', 'currency_id', 'country_code', 'min_users', 'max_users',
        'price_per_user', 'base_price', 'billing_cycle', 'pricing_model',
        'tier_discounts', 'valid_from', 'valid_until', 'metadata', 'status'
    ];

    protected $casts = [
        'min_users' => 'integer', 'max_users' => 'integer',
        'price_per_user' => 'decimal:2', 'base_price' => 'decimal:2',
        'tier_discounts' => 'array', 'valid_from' => 'date', 'valid_until' => 'date',
        'metadata' => 'array',
    ];

    public function plan() { return $this->belongsTo(Plan::class); }
    public function currency() { return $this->belongsTo(Currency::class); }

    public function calculatePrice($userCount)
    {
        if ($userCount < $this->min_users || ($this->max_users && $userCount > $this->max_users)) {
            return null;
        }

        $price = $this->base_price + ($userCount * $this->price_per_user);

        if ($this->pricing_model === 'tiered' && $this->tier_discounts) {
            foreach ($this->tier_discounts as $tier) {
                if ($userCount >= $tier['min_users']) {
                    $price *= (1 - $tier['discount'] / 100);
                }
            }
        }

        return $price;
    }
}
