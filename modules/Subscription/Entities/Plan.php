<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class Plan extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    protected $connection = 'landlord';

    public $singleTitle = "plan";
    public $pluralTitle = "plans";

    protected $fileColumns = [
        'icon' => [
            'folder' => 'plans',
            'is_encrypted' => false,
            'access_level' => 'public',
            'metadata' => ['width', 'height', 'aspect_ratio'],
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'icon',
        'priority'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function features()
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function discounts()
    {
        return $this->hasMany(PlanDiscount::class);
    }

    public function billingCycles()
    {
        return $this->hasMany(PlanBillingCycle::class);
    }
}
