<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\ValueObjects;

enum ShareChannel: string
{
    case EMAIL = 'email';
    case LINK = 'link';
    case EMBED = 'embed';
    case SMS = 'sms';
    case QR_CODE = 'qr_code';
    case SOCIAL = 'social';

    public static function fromString(string $value): self
    {
        return match($value) {
            'email' => self::EMAIL,
            'link' => self::LINK,
            'embed' => self::EMBED,
            'sms' => self::SMS,
            'qr_code' => self::QR_CODE,
            'social' => self::SOCIAL,
            default => self::LINK,
        };
    }

    public function label(): string
    {
        return match($this) {
            self::EMAIL => 'Email',
            self::LINK => 'Link',
            self::EMBED => 'Embed',
            self::SMS => 'SMS',
            self::QR_CODE => 'QR Code',
            self::SOCIAL => 'Social',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::EMAIL => 'Mail',
            self::LINK => 'Link',
            self::EMBED => 'Code',
            self::SMS => 'MessageSquare',
            self::QR_CODE => 'QrCode',
            self::SOCIAL => 'Share2',
        };
    }

    public function requiresDistributionStrategy(): bool
    {
        return $this !== self::LINK && $this !== self::QR_CODE;
    }

    public function generatesPublicUrl(): bool
    {
        return in_array($this, [self::LINK, self::EMBED, self::SOCIAL, self::QR_CODE], true);
    }
}
