<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Utilities\Entities\Currency;
use App\Models\User;

class PlanPriceHistory extends Model
{
    use HasFactory;

    protected $connection = "landlord";

    protected $fillable = [
        'plan_id', 'currency_id', 'country_code', 'billing_cycle', 'old_price',
        'new_price', 'old_setup_fee', 'new_setup_fee', 'change_date',
        'effective_date', 'change_type', 'change_reason', 'changed_by',
        'metadata', 'status'
    ];

    protected $casts = [
        'old_price' => 'decimal:2', 'new_price' => 'decimal:2',
        'old_setup_fee' => 'decimal:2', 'new_setup_fee' => 'decimal:2',
        'change_date' => 'datetime', 'effective_date' => 'datetime',
        'metadata' => 'array',
    ];

    public function plan() { return $this->belongsTo(Plan::class); }
    public function currency() { return $this->belongsTo(Currency::class); }
    public function changedBy() { return $this->belongsTo(User::class, 'changed_by'); }
}
