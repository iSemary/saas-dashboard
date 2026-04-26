<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmAutomationRule extends Model
{
    use SoftDeletes;

    protected $table = 'em_automation_rules';

    protected $fillable = [
        'name', 'trigger_type', 'conditions', 'action_type', 'action_config', 'is_active', 'created_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'action_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function toggle(): void
    {
        $this->is_active = ! $this->is_active;
    }
}
