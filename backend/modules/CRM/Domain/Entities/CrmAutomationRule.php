<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

/**
 * Automation rule that triggers actions based on events and conditions.
 */
class CrmAutomationRule extends Model
{
    protected $table = 'crm_automation_rules';

    protected $fillable = [
        'name',
        'trigger_event',
        'conditions',
        'actions',
        'is_active',
        'priority',
        'created_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the user who created this rule.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get rules for a specific trigger event.
     */
    public function scopeForEvent($query, string $event)
    {
        return $query->where('trigger_event', $event);
    }

    /**
     * Scope: Get ordered by priority (descending).
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Business method: Enable the rule.
     */
    public function enable(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Business method: Disable the rule.
     */
    public function disable(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Toggle active state.
     */
    public function toggle(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Check if conditions match the given context.
     */
    public function conditionsMatch(array $context): bool
    {
        $conditions = $this->conditions ?? [];

        // If no conditions, always match
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            if (!$this->evaluateCondition($condition, $context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate a single condition.
     */
    private function evaluateCondition(array $condition, array $context): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? 'equals';
        $value = $condition['value'] ?? null;

        if ($field === null) {
            return true;
        }

        $contextValue = $context[$field] ?? null;

        return match ($operator) {
            'equals' => $contextValue == $value,
            'not_equals' => $contextValue != $value,
            'contains' => str_contains((string) $contextValue, (string) $value),
            'greater_than' => $contextValue > $value,
            'less_than' => $contextValue < $value,
            'empty' => empty($contextValue),
            'not_empty' => !empty($contextValue),
            default => false,
        };
    }

    /**
     * Get actions to execute.
     *
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions ?? [];
    }

    /**
     * Check if this rule should trigger for the given event.
     */
    public function shouldTrigger(string $event, array $context): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->trigger_event === $event && $this->conditionsMatch($context);
    }
}
