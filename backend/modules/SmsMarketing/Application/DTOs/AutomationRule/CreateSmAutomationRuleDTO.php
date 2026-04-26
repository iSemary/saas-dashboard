<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\DTOs\AutomationRule;

class CreateSmAutomationRuleDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $trigger_type,
        public readonly ?array $conditions = null,
        public readonly string $action_type = 'send_campaign',
        public readonly ?array $action_config = null,
        public readonly bool $is_active = true,
    ) {}

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }
}
