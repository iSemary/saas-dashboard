<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PlanBillingCycle extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    protected $fillable = [
        'plan_id',
        'billing_cycle',
        'price',
        'currency_id',
        'status'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
