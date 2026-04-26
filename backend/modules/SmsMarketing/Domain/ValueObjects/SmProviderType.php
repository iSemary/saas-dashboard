<?php

namespace Modules\SmsMarketing\Domain\ValueObjects;

enum SmProviderType: string
{
    case Twilio = 'twilio';
    case Vonage = 'vonage';
    case MessageBird = 'messagebird';
    case Mock = 'mock';

    public function label(): string
    {
        return match ($this) {
            self::Twilio => 'Twilio',
            self::Vonage => 'Vonage',
            self::MessageBird => 'MessageBird',
            self::Mock => 'Mock (Log only)',
        };
    }
}
