<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'em_templates';

    protected $fillable = [
        'name', 'subject', 'body_html', 'body_text', 'thumbnail_url',
        'category', 'variables', 'status', 'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public function campaigns()
    {
        return $this->hasMany(EmCampaign::class, 'template_id');
    }
}
