<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\Survey\Infrastructure\Persistence\SurveyShareRepositoryInterface;

class EmbedSurveyController extends Controller
{
    public function __construct(
        private SurveyShareRepositoryInterface $shareRepository
    ) {}

    public function show(string $token): Response
    {
        $share = $this->shareRepository->findByToken($token);

        if (!$share || !$share->isEmbedChannel()) {
            return response('Survey not found or not available for embed', 404);
        }

        if ($share->isExpired()) {
            return response('Survey link has expired', 410);
        }

        $survey = $share->survey;
        $publicUrl = $share->getPublicUrl();
        $width = $share->config['width'] ?? '100%';
        $height = $share->config['height'] ?? '600px';

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>{$survey->title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { margin: 0; padding: 0; }
        iframe { border: none; width: {$width}; height: {$height}; }
    </style>
</head>
<body>
    <iframe src="{$publicUrl}" allowfullscreen></iframe>
</body>
</html>
HTML;

        return response($html, 200, ['Content-Type' => 'text/html']);
    }
}
