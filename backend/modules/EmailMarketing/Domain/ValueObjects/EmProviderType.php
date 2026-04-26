<?php

namespace Modules\EmailMarketing\Domain\ValueObjects;

enum EmProviderType: string
{
    case Smtp = 'smtp';
    case Ses = 'ses';
    case Mailgun = 'mailgun';
    case Sendgrid = 'sendgrid';
    case Postmark = 'postmark';

    public function label(): string
    {
        return match ($this) {
            self::Smtp => 'SMTP',
            self::Ses => 'Amazon SES',
            self::Mailgun => 'Mailgun',
            self::Sendgrid => 'SendGrid',
            self::Postmark => 'Postmark',
        };
    }
}
