<?php

declare(strict_types=1);

namespace Modules\Survey\Application\UseCases;

use Modules\Survey\Application\DTOs\SubmitResponseData;
use Modules\Survey\Domain\Entities\SurveyResponse;
use Modules\Survey\Infrastructure\Persistence\SurveyResponseRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyShareRepositoryInterface;
use Modules\Survey\Domain\Exceptions\SurveyClosedException;
use Modules\Survey\Domain\Entities\Survey;

class SubmitResponse
{
    public function __construct(
        private SurveyResponseRepositoryInterface $responseRepository,
        private SurveyShareRepositoryInterface $shareRepository,
    ) {}

    public function execute(SubmitResponseData $data, Survey $survey): SurveyResponse
    {
        // Check survey is active
        if (!$survey->isActive()) {
            throw new SurveyClosedException();
        }

        $responseData = $data->toArray();

        // Resolve share_id from token if provided
        if ($data->shareToken) {
            $share = $this->shareRepository->findByToken($data->shareToken);
            if ($share) {
                $share->checkValid();
                $responseData['share_id'] = $share->id;
                $this->shareRepository->incrementUses($share->id);
            }
        }

        return $this->responseRepository->create($responseData);
    }
}
