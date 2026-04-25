<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\AutomationAction;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Domain\Exceptions\AutomationExecutionException;

/**
 * Send an in-app notification to users.
 */
class SendNotificationAction implements AutomationActionStrategyInterface
{
    public function supports(string $actionType): bool
    {
        return $actionType === 'send_notification';
    }

    public function execute(array $actionConfig, object $model, array $context): void
    {
        $title = $actionConfig['title'] ?? 'Automation Notification';
        $message = $actionConfig['message'] ?? null;
        $recipients = $actionConfig['recipients'] ?? [];
        $notifyAssigned = $actionConfig['notify_assigned'] ?? true;

        if ($message === null) {
            throw new AutomationExecutionException('Message is required for send_notification action');
        }

        // Get recipient IDs
        $recipientIds = [];

        if ($notifyAssigned && $model instanceof Model && $model->assigned_to) {
            $recipientIds[] = $model->assigned_to;
        }

        foreach ($recipients as $recipient) {
            if (is_numeric($recipient)) {
                $recipientIds[] = (int) $recipient;
            } elseif ($recipient === 'triggered_by') {
                $recipientIds[] = $context['triggered_by'] ?? auth()->id();
            }
        }

        $recipientIds = array_unique(array_filter($recipientIds));

        if (empty($recipientIds)) {
            throw new AutomationExecutionException('No recipients found for notification');
        }

        // Use Notification module to send notifications
        $notificationService = app(\Modules\Notification\Services\NotificationService::class);

        foreach ($recipientIds as $userId) {
            $notificationService->createNotification([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'model_type' => get_class($model),
                    'model_id' => $model->id ?? null,
                    'automation_trigger' => $context['trigger_event'] ?? null,
                ],
            ]);
        }
    }

    public function getName(): string
    {
        return 'Send Notification';
    }

    public function getDescription(): string
    {
        return 'Send an in-app notification to assigned users or specified recipients.';
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'title',
                'type' => 'string',
                'required' => true,
                'label' => 'Notification Title',
            ],
            [
                'name' => 'message',
                'type' => 'textarea',
                'required' => true,
                'label' => 'Message',
            ],
            [
                'name' => 'notify_assigned',
                'type' => 'boolean',
                'required' => false,
                'label' => 'Notify Assigned User',
                'default' => true,
            ],
            [
                'name' => 'recipients',
                'type' => 'multiselect',
                'required' => false,
                'label' => 'Additional Recipients',
                'help' => 'User IDs or "triggered_by"',
            ],
        ];
    }
}
