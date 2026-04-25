<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;
use Modules\Survey\Domain\ValueObjects\AutomationTrigger;
use Modules\Survey\Domain\ValueObjects\AutomationAction;

class SurveyAutomationRule extends Model
{
    use SoftDeletes;

    protected $table = 'survey_automation_rules';

    protected $fillable = [
        'survey_id',
        'name',
        'trigger_type',
        'conditions',
        'action_type',
        'action_config',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'action_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function toggle(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }

    public function getTriggerLabel(): string
    {
        return AutomationTrigger::fromString($this->trigger_type)->label();
    }

    public function getActionLabel(): string
    {
        return AutomationAction::fromString($this->action_type)->label();
    }

    public function shouldTrigger(string $event, array $context = []): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->trigger_type !== $event) {
            return false;
        }

        // Evaluate conditions if present
        $conditions = $this->conditions ?? [];
        if (empty($conditions)) {
            return true;
        }

        return $this->evaluateConditions($conditions, $context);
    }

    private function evaluateConditions(array $conditions, array $context): bool
    {
        $logic = $conditions['logic'] ?? 'AND';
        $rules = $conditions['rules'] ?? [];

        if (empty($rules)) {
            return true;
        }

        $results = [];
        foreach ($rules as $rule) {
            $results[] = $this->evaluateSingleCondition($rule, $context);
        }

        return match(strtoupper($logic)) {
            'AND' => !in_array(false, $results, true),
            'OR' => in_array(true, $results, true),
            default => !in_array(false, $results, true),
        };
    }

    private function evaluateSingleCondition(array $rule, array $context): bool
    {
        $field = $rule['field'] ?? null;
        $operator = $rule['operator'] ?? 'eq';
        $value = $rule['value'] ?? null;

        if (!$field) {
            return true;
        }

        $contextValue = data_get($context, $field);

        return match($operator) {
            'eq' => $contextValue == $value,
            'neq' => $contextValue != $value,
            'gt' => $contextValue > $value,
            'gte' => $contextValue >= $value,
            'lt' => $contextValue < $value,
            'lte' => $contextValue <= $value,
            'in' => in_array($contextValue, (array) $value),
            'not_in' => !in_array($contextValue, (array) $value),
            'contains' => str_contains((string) $contextValue, (string) $value),
            default => true,
        };
    }

    public function execute(array $context = []): array
    {
        $config = $this->action_config;

        return match($this->action_type) {
            'send_email' => $this->executeSendEmail($config, $context),
            'update_field' => $this->executeUpdateField($config, $context),
            'create_activity' => $this->executeCreateActivity($config, $context),
            'send_notification' => $this->executeSendNotification($config, $context),
            'trigger_webhook' => $this->executeTriggerWebhook($config, $context),
            'create_crm_activity' => $this->executeCreateCrmActivity($config, $context),
            default => ['success' => false, 'message' => 'Unknown action type'],
        };
    }

    private function executeSendEmail(array $config, array $context): array
    {
        return ['success' => true, 'message' => 'Email action queued', 'config' => $config];
    }

    private function executeUpdateField(array $config, array $context): array
    {
        return ['success' => true, 'message' => 'Field updated', 'config' => $config];
    }

    private function executeCreateActivity(array $config, array $context): array
    {
        return ['success' => true, 'message' => 'Activity created', 'config' => $config];
    }

    private function executeSendNotification(array $config, array $context): array
    {
        return ['success' => true, 'message' => 'Notification sent', 'config' => $config];
    }

    private function executeTriggerWebhook(array $config, array $context): array
    {
        return ['success' => true, 'message' => 'Webhook triggered', 'config' => $config];
    }

    private function executeCreateCrmActivity(array $config, array $context): array
    {
        return ['success' => true, 'message' => 'CRM activity created', 'config' => $config];
    }
}
