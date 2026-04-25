<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum AutomationAction: string
{
    case SEND_EMAIL = 'send_email';
    case UPDATE_FIELD = 'update_field';
    case CREATE_ACTIVITY = 'create_activity';
    case SEND_NOTIFICATION = 'send_notification';
    case TRIGGER_WEBHOOK = 'trigger_webhook';
    case CREATE_CRM_ACTIVITY = 'create_crm_activity';

    public static function fromString(string $value): self
    {
        return match($value) {
            'send_email' => self::SEND_EMAIL,
            'update_field' => self::UPDATE_FIELD,
            'create_activity' => self::CREATE_ACTIVITY,
            'send_notification' => self::SEND_NOTIFICATION,
            'trigger_webhook' => self::TRIGGER_WEBHOOK,
            'create_crm_activity' => self::CREATE_CRM_ACTIVITY,
            default => self::SEND_EMAIL,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::SEND_EMAIL => 'Send Email',
            self::UPDATE_FIELD => 'Update Field',
            self::CREATE_ACTIVITY => 'Create Activity',
            self::SEND_NOTIFICATION => 'Send Notification',
            self::TRIGGER_WEBHOOK => 'Trigger Webhook',
            self::CREATE_CRM_ACTIVITY => 'Create CRM Activity',
        };
    }

    public function requiresCrossModuleIntegration(): bool
    {
        return in_array($this, [
            self::SEND_EMAIL,
            self::SEND_NOTIFICATION,
            self::CREATE_CRM_ACTIVITY,
            self::TRIGGER_WEBHOOK,
        ], true);
    }

    public function requiresConfig(): bool
    {
        return true;
    }

    public function defaultConfig(): array
    {
        return match($this) {
            self::SEND_EMAIL => ['to' => null, 'template' => null, 'subject' => null],
            self::UPDATE_FIELD => ['field' => null, 'value' => null],
            self::CREATE_ACTIVITY => ['activity_type' => 'survey_response', 'description' => null],
            self::SEND_NOTIFICATION => ['channel' => 'push', 'message' => null],
            self::TRIGGER_WEBHOOK => ['webhook_id' => null],
            self::CREATE_CRM_ACTIVITY => ['activity_type' => 'survey_response', 'contact_match_field' => 'email'],
        };
    }
}
