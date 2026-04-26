<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'sm_templates';

    protected $fillable = [
        'name', 'body', 'variables', 'status', 'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public function campaigns()
    {
        return $this->hasMany(SmCampaign::class, 'template_id');
    }
}
