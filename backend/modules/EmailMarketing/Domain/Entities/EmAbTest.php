<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmAbTest extends Model
{
    use SoftDeletes;

    protected $table = 'em_ab_tests';

    protected $fillable = [
        'name', 'variant_name', 'subject', 'body_html', 'percentage', 'winner', 'stats', 'created_by',
    ];

    protected $casts = [
        'stats' => 'array',
        'percentage' => 'integer',
    ];

    public function campaigns()
    {
        return $this->hasMany(EmCampaign::class, 'ab_test_id');
    }
}
