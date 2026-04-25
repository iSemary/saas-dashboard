<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\AutomationAction;

/**
 * Interface for automation action strategies.
 * Each action type (assign user, update field, etc.) implements this interface.
 */
interface AutomationActionStrategyInterface
{
    /**
     * Check if this strategy supports the given action type.
     */
    public function supports(string $actionType): bool;

    /**
     * Execute the automation action.
     *
     * @param array $actionConfig Configuration for this action (e.g., ['field' => 'status', 'value' => 'qualified'])
     * @param object $model The entity being processed (Lead, Contact, Opportunity, etc.)
     * @param array $context Additional context (trigger event, user, timestamp, etc.)
     */
    public function execute(array $actionConfig, object $model, array $context): void;

    /**
     * Get the action name for display.
     */
    public function getName(): string;

    /**
     * Get the action description.
     */
    public function getDescription(): string;

    /**
     * Get the required configuration fields for this action.
     *
     * @return array Array of field definitions ['name', 'type', 'required', 'options' (if applicable)]
     */
    public function getConfigFields(): array;
}
