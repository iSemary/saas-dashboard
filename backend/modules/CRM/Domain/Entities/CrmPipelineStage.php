<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Domain\ValueObjects\OpportunityStage;

/**
 * Configurable pipeline stage for opportunities.
 */
class CrmPipelineStage extends Model
{
    protected $table = 'crm_pipeline_stages';

    protected $fillable = [
        'name',
        'key',
        'position',
        'probability',
        'is_default',
        'color',
    ];

    protected $casts = [
        'position' => 'integer',
        'probability' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    /**
     * Get opportunities in this stage.
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class, 'stage', 'key');
    }

    /**
     * Scope: Get default stage.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: Get ordered stages.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Business method: Set as default stage.
     */
    public function setAsDefault(): void
    {
        // Remove default from all other stages
        self::where('is_default', true)->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Business method: Move to new position.
     */
    public function reorder(int $newPosition): void
    {
        $oldPosition = $this->position;

        if ($newPosition > $oldPosition) {
            // Moving down: decrement positions in between
            self::where('position', '>', $oldPosition)
                ->where('position', '<=', $newPosition)
                ->decrement('position');
        } else {
            // Moving up: increment positions in between
            self::where('position', '>=', $newPosition)
                ->where('position', '<', $oldPosition)
                ->increment('position');
        }

        $this->update(['position' => $newPosition]);
    }

    /**
     * Get stage as Value Object.
     */
    public function toValueObject(): OpportunityStage
    {
        return OpportunityStage::tryFrom($this->key) ?? OpportunityStage::PROSPECTING;
    }

    /**
     * Get CSS color class.
     */
    public function colorClass(): string
    {
        return $this->color ?? 'gray';
    }

    /**
     * Get formatted probability.
     */
    public function formattedProbability(): string
    {
        return $this->probability . '%';
    }
}
