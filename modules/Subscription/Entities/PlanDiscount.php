<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PlanDiscount extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = 'landlord';

    protected $fillable = [
        'plan_id',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'status'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
