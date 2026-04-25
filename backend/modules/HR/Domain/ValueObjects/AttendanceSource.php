<?php

namespace Modules\HR\Domain\ValueObjects;

enum AttendanceSource: string
{
    case WEB = 'web';
    case MOBILE = 'mobile';
    case BIOMETRIC = 'biometric';
    case MANUAL = 'manual';
    case API = 'api';

    public function label(): string
    {
        return match ($this) {
            self::WEB => 'Web',
            self::MOBILE => 'Mobile App',
            self::BIOMETRIC => 'Biometric Device',
            self::MANUAL => 'Manual Entry',
            self::API => 'API',
        };
    }
}
