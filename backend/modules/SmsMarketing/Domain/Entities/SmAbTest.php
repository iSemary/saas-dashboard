<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmAbTest extends Model
{
    use SoftDeletes;

    protected $table = 'sm_ab_tests';

    protected $fillable = [
        'name', 'variant_name', 'body', 'percentage', 'winner', 'stats', 'created_by',
    ];

    protected $casts = [
        'stats' => 'array',
        'percentage' => 'integer',
    ];

    public function campaigns()
    {
        return $this->hasMany(SmCampaign::class, 'ab_test_id');
    }
}
