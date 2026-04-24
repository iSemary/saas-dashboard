<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanUpgradeRule extends Model
{
    use HasFactory;

    protected $connection = "landlord";

    protected $fillable = [
        'from_plan_id', 'to_plan_id', 'rule_type', 'is_allowed', 'proration_type',
        'upgrade_fee', 'downgrade_credit', 'restriction_days', 'max_changes_per_period',
        'required_conditions', 'change_description', 'user_message',
        'requires_approval', 'metadata', 'status'
    ];

    protected $casts = [
        'is_allowed' => 'boolean', 'upgrade_fee' => 'decimal:2', 'downgrade_credit' => 'decimal:2',
        'restriction_days' => 'integer', 'max_changes_per_period' => 'integer',
        'required_conditions' => 'array', 'requires_approval' => 'boolean', 'metadata' => 'array',
    ];

    public function fromPlan() { return $this->belongsTo(Plan::class, 'from_plan_id'); }
    public function toPlan() { return $this->belongsTo(Plan::class, 'to_plan_id'); }
}
