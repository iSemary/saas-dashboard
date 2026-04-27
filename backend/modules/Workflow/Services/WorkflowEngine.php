<?php

namespace Modules\Workflow\Services;

use Modules\Workflow\Models\WorkflowDefinition;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    /**
     * Trigger a workflow based on an event.
     */
    public function triggerWorkflow(string $event, $model, array $context = []): void
    {
        $module = $this->getModuleFromModel($model);
        $triggerEvent = $this->formatTriggerEvent($event, $module);

        $workflows = WorkflowDefinition::where('module', $module)
            ->where('trigger_event', $triggerEvent)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($workflows as $workflow) {
            if ($this->evaluateConditions($workflow, $model, $context)) {
                $this->executeWorkflow($workflow, $model, $context);
            }
        }
    }

    /**
     * Execute a workflow.
     */
    public function executeWorkflow(WorkflowDefinition $workflow, $model, array $context = []): WorkflowInstance
    {
        $instance = WorkflowInstance::create([
            'workflow_definition_id' => $workflow->id,
            'related_type' => get_class($model),
            'related_id' => $model->id,
            'status' => 'running',
            'current_step' => 0,
            'context' => $context,
            'variables' => [],
            'started_at' => now(),
            'created_by' => auth()->id() ?? 1,
        ]);

        $this->processWorkflowSteps($instance);

        return $instance;
    }

    /**
     * Process workflow steps.
     */
    protected function processWorkflowSteps(WorkflowInstance $instance): void
    {
        $workflow = $instance->workflowDefinition;
        $steps = $workflow->steps;
        $variables = $instance->variables ?? [];

        foreach ($steps as $index => $stepConfig) {
            $step = WorkflowStep::create([
                'workflow_instance_id' => $instance->id,
                'step_number' => $index + 1,
                'step_type' => $stepConfig['type'],
                'step_name' => $stepConfig['name'],
                'step_config' => $stepConfig,
                'status' => 'pending',
            ]);

            try {
                $this->executeStep($step, $instance, $variables);
            } catch (\Exception $e) {
                $step->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                ]);

                $instance->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                ]);

                Log::error('Workflow step failed', [
                    'workflow_instance_id' => $instance->id,
                    'step_id' => $step->id,
                    'error' => $e->getMessage(),
                ]);

                return;
            }
        }

        $instance->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Execute a workflow step.
     */
    protected function executeStep(WorkflowStep $step, WorkflowInstance $instance, array &$variables): void
    {
        $step->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        $config = $step->step_config;
        $model = $instance->related;

        switch ($step->step_type) {
            case 'action':
                $this->executeAction($step, $model, $config, $variables);
                break;
            case 'condition':
                $this->executeCondition($step, $model, $config, $variables);
                break;
            case 'delay':
                $this->executeDelay($step, $config);
                break;
            case 'notification':
                $this->executeNotification($step, $model, $config);
                break;
            default:
                throw new \Exception("Unknown step type: {$step->step_type}");
        }

        $step->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Execute an action step.
     */
    protected function executeAction(WorkflowStep $step, $model, array $config, array &$variables): void
    {
        $action = $config['action'];

        switch ($action) {
            case 'update_field':
                $field = $config['field'];
                $value = $this->evaluateExpression($config['value'], $variables);
                $model->update([$field => $value]);
                break;
            case 'assign_user':
                $userId = $this->evaluateExpression($config['user_id'], $variables);
                $model->update(['assigned_to' => $userId]);
                break;
            case 'create_activity':
                $this->createActivity($model, $config, $variables);
                break;
            default:
                throw new \Exception("Unknown action: {$action}");
        }
    }

    /**
     * Execute a condition step.
     */
    protected function executeCondition(WorkflowStep $step, $model, array $config, array &$variables): void
    {
        $condition = $config['condition'];
        $result = $this->evaluateCondition($condition, $model, $variables);

        if (!$result) {
            $step->update([
                'status' => 'skipped',
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Execute a delay step.
     */
    protected function executeDelay(WorkflowStep $step, array $config): void
    {
        $delay = $config['delay']; // in minutes
        sleep($delay * 60); // Simple delay implementation
    }

    /**
     * Execute a notification step.
     */
    protected function executeNotification(WorkflowStep $step, $model, array $config): void
    {
        // Implementation for sending notifications
        // This would integrate with the notification system
    }

    /**
     * Create an activity.
     */
    protected function createActivity($model, array $config, array $variables): void
    {
        $subject = $this->evaluateExpression($config['subject'], $variables);
        $description = $this->evaluateExpression($config['description'] ?? '', $variables);

        // Create audit entry instead of activity
        $auditData = [
            'event' => 'workflow_activity',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => [],
            'new_values' => [
                'workflow_subject' => $subject,
                'workflow_description' => $description,
                'workflow_type' => $config['type'] ?? 'note',
                'assigned_to' => $this->evaluateExpression($config['assigned_to'] ?? null, $variables),
            ],
            'user_id' => auth()->id() ?? 1,
            'user_type' => 'Modules\\Auth\\Entities\\User',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        // Create audit entry using OwenIt Auditing
        if (class_exists('OwenIt\\Auditing\\Models\\Audit')) {
            \OwenIt\Auditing\Models\Audit::create($auditData);
        }
    }

    /**
     * Evaluate workflow conditions.
     */
    protected function evaluateConditions(WorkflowDefinition $workflow, $model, array $context): bool
    {
        if (!$workflow->conditions) {
            return true;
        }

        return $this->evaluateCondition($workflow->conditions, $model, $context);
    }

    /**
     * Evaluate a condition.
     */
    protected function evaluateCondition($condition, $model, array $variables): bool
    {
        if (is_string($condition)) {
            return $this->evaluateExpression($condition, array_merge($variables, ['model' => $model]));
        }

        if (is_array($condition)) {
            $operator = $condition['operator'] ?? 'and';
            $conditions = $condition['conditions'] ?? [];

            $results = array_map(function ($cond) use ($model, $variables) {
                return $this->evaluateCondition($cond, $model, $variables);
            }, $conditions);

            return $operator === 'and' ? !in_array(false, $results) : in_array(true, $results);
        }

        return true;
    }

    /**
     * Evaluate an expression.
     */
    protected function evaluateExpression(string $expression, array $variables): mixed
    {
        // Simple expression evaluation
        // In a real implementation, you'd use a proper expression evaluator
        foreach ($variables as $key => $value) {
            $expression = str_replace("{{$key}}", $value, $expression);
        }

        return $expression;
    }

    /**
     * Get module name from model.
     */
    protected function getModuleFromModel($model): string
    {
        $class = get_class($model);
        $parts = explode('\\', $class);

        if (count($parts) >= 2 && $parts[0] === 'Modules') {
            return strtolower($parts[1]);
        }

        return 'unknown';
    }

    /**
     * Format trigger event.
     */
    protected function formatTriggerEvent(string $event, string $module): string
    {
        return "{$module}.{$event}";
    }
}
