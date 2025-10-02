<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Localization\Traits\Translatable;
use OwenIt\Auditing\Contracts\Auditable;

class PlanFeature extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable, Translatable;

    protected $connection = "landlord";

    protected $fillable = [
        'plan_id',
        'feature_key',
        'name',
        'description',
        'feature_type',
        'feature_value',
        'numeric_limit',
        'is_unlimited',
        'unit',
        'sort_order',
        'is_highlighted',
        'metadata',
        'status'
    ];

    protected $translatableColumns = ['name', 'description'];

    protected $casts = [
        'numeric_limit' => 'integer',
        'is_unlimited' => 'boolean',
        'sort_order' => 'integer',
        'is_highlighted' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the plan that owns the feature.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Scope for active features.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for highlighted features.
     */
    public function scopeHighlighted($query)
    {
        return $query->where('is_highlighted', true);
    }

    /**
     * Scope for ordered features.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the parsed feature value based on type.
     */
    public function getParsedValueAttribute()
    {
        switch ($this->feature_type) {
            case 'boolean':
                return (bool) $this->feature_value;
            case 'numeric':
                return $this->is_unlimited ? -1 : $this->numeric_limit;
            case 'json':
                return json_decode($this->feature_value, true);
            default:
                return $this->feature_value;
        }
    }

    /**
     * Get formatted display value.
     */
    public function getDisplayValueAttribute()
    {
        switch ($this->feature_type) {
            case 'boolean':
                return $this->parsed_value ? '✓' : '✗';
            case 'numeric':
                if ($this->is_unlimited) {
                    return 'Unlimited';
                }
                return number_format($this->numeric_limit) . ($this->unit ? ' ' . $this->unit : '');
            default:
                return $this->feature_value;
        }
    }

    /**
     * Check if feature allows unlimited usage.
     */
    public function isUnlimited()
    {
        return $this->is_unlimited || $this->numeric_limit === -1;
    }

    /**
     * Check if feature is enabled (for boolean features).
     */
    public function isEnabled()
    {
        return $this->feature_type === 'boolean' && $this->parsed_value;
    }

    /**
     * Get numeric limit or return unlimited indicator.
     */
    public function getLimit()
    {
        if ($this->feature_type !== 'numeric') {
            return null;
        }

        return $this->is_unlimited ? -1 : $this->numeric_limit;
    }
}
