<?php

declare(strict_types=1);

namespace Modules\Survey\Application\UseCases;

use Modules\Survey\Application\DTOs\CreateSurveyShareData;
use Modules\Survey\Domain\Entities\SurveyShare;
use Modules\Survey\Infrastructure\Persistence\SurveyShareRepositoryInterface;

class CreateSurveyShare
{
    public function __construct(
        private SurveyShareRepositoryInterface $shareRepository
    ) {}

    public function execute(CreateSurveyShareData $data): SurveyShare
    {
        $shareData = $data->toArray();
        $shareData['token'] = $this->generateToken();
        $shareData['uses_count'] = 0;

        return $this->shareRepository->create($shareData);
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
