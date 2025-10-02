<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanTrial extends Model
{
    use HasFactory;

    protected $connection = "landlord";

    protected $fillable = [
        'plan_id', 'country_code', 'trial_days', 'requires_payment_method',
        'auto_convert', 'trial_type', 'trial_price', 'trial_features',
        'trial_limits', 'trial_terms', 'allow_multiple_trials',
        'grace_period_days', 'metadata', 'status'
    ];

    protected $casts = [
        'trial_days' => 'integer', 'requires_payment_method' => 'boolean',
        'auto_convert' => 'boolean', 'trial_price' => 'decimal:2',
        'trial_limits' => 'array', 'allow_multiple_trials' => 'boolean',
        'grace_period_days' => 'integer', 'metadata' => 'array',
    ];

    public function plan() { return $this->belongsTo(Plan::class); }
}
