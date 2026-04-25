<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Survey\Application\DTOs\SubmitResponseData;
use Modules\Survey\Application\DTOs\SubmitAnswerData;
use Modules\Survey\Application\UseCases\SubmitResponse;
use Modules\Survey\Application\UseCases\SubmitAnswer;
use Modules\Survey\Application\UseCases\CompleteResponse;
use Modules\Survey\Infrastructure\Persistence\SurveyShareRepositoryInterface;
use Modules\Survey\Infrastructure\Persistence\SurveyResponseRepositoryInterface;
use Modules\Survey\Domain\Exceptions\SurveyClosedException;

class PublicSurveyController extends ApiController
{
    public function __construct(
        private SurveyShareRepositoryInterface $shareRepository,
        private SurveyResponseRepositoryInterface $responseRepository,
        private SubmitResponse $submitResponse,
        private SubmitAnswer $submitAnswer,
        private CompleteResponse $completeResponse,
    ) {}

    public function show(string $token): JsonResponse
    {
        $share = $this->shareRepository->findByToken($token);
        if (!$share) {
            return $this->respondNotFound('Survey not found');
        }

        try {
            $share->checkValid();
        } catch (\Exception $e) {
            return $this->respondError($e->getMessage(), 410);
        }

        $survey = $share->survey;
        if (!$survey->isActive()) {
            return $this->respondError('Survey is not active', 403);
        }

        return $this->respondWithArray([
            'survey' => $survey->load(['pages.questions.options', 'theme']),
            'share' => $share,
        ]);
    }

    public function start(Request $request, string $token): JsonResponse
    {
        $share = $this->shareRepository->findByToken($token);
        if (!$share) {
            return $this->respondNotFound('Survey not found');
        }

        $data = SubmitResponseData::fromArray([
            'survey_id' => $share->survey_id,
            'share_token' => $token,
            'respondent_type' => $request->get('respondent_type', 'anonymous'),
            'respondent_email' => $request->get('respondent_email'),
            'respondent_name' => $request->get('respondent_name'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'locale' => $request->get('locale', 'en'),
        ]);

        try {
            $response = $this->submitResponse->execute($data, $share->survey);
            return $this->respondCreated([
                'response' => $response,
                'resume_token' => $response->resume_token,
            ]);
        } catch (SurveyClosedException $e) {
            return $this->respondError($e->getMessage(), 403);
        }
    }

    public function answer(Request $request, string $token): JsonResponse
    {
        $data = SubmitAnswerData::fromArray($request->all());
        $answer = $this->submitAnswer->execute($data);
        return $this->respondWithArray(['data' => $answer]);
    }

    public function complete(Request $request, string $token): JsonResponse
    {
        $responseId = $request->get('response_id');
        $response = $this->completeResponse->execute($responseId);
        return $this->respondWithArray(['data' => $response]);
    }

    public function resume(string $token, string $resumeToken): JsonResponse
    {
        $response = $this->responseRepository->findByToken($resumeToken);
        if (!$response || $response->survey->shares()->where('token', $token)->doesntExist()) {
            return $this->respondNotFound('Response not found');
        }

        if (!$response->canResume()) {
            return $this->respondError('Response cannot be resumed', 403);
        }

        return $this->respondWithArray([
            'response' => $response,
            'answers' => $response->answers,
        ]);
    }
}
