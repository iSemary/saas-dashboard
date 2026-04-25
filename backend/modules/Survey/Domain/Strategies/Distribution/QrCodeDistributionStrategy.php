<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Distribution;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyShare;

class QrCodeDistributionStrategy implements DistributionStrategyInterface
{
    public function getName(): string
    {
        return 'qr_code';
    }

    public function getLabel(): string
    {
        return 'QR Code';
    }

    public function distribute(Survey $survey, SurveyShare $share, array $recipients): array
    {
        $url = $share->getPublicUrl();

        // QR code generation would be handled by a service
        // $qrCode = QRCodeService::generate($url);

        return [
            [
                'url' => $url,
                'qr_code_image_url' => null, // Would be generated
                'status' => 'ready',
                'channel' => 'qr_code',
            ],
        ];
    }

    public function validateConfig(array $config): bool
    {
        return true;
    }
}
