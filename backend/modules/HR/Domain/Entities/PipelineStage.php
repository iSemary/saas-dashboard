<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipelineStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pipeline_stages';

    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'order',
        'maps_to_status',
        'is_default',
        'requires_interview',
        'requires_approval',
        'auto_email_template_id',
        'created_by',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_default' => 'boolean',
        'requires_interview' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
