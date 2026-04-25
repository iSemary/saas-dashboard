<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyAnswer;

class EloquentSurveyAnswerRepository implements SurveyAnswerRepositoryInterface
{
    public function find(int $id): ?SurveyAnswer
    {
        return SurveyAnswer::find($id);
    }

    public function create(array $data): SurveyAnswer
    {
        return SurveyAnswer::create($data);
    }

    public function update(int $id, array $data): SurveyAnswer
    {
        $answer = SurveyAnswer::findOrFail($id);
        $answer->update($data);
        return $answer->fresh();
    }

    public function delete(int $id): bool
    {
        $answer = $this->find($id);
        return $answer ? $answer->delete() : false;
    }

    public function findByResponse(int $responseId): array
    {
        return SurveyAnswer::where('response_id', $responseId)
            ->with('question')
            ->get()
            ->toArray();
    }

    public function findByQuestion(int $questionId): array
    {
        return SurveyAnswer::where('question_id', $questionId)->get()->toArray();
    }

    public function findByResponseAndQuestion(int $responseId, int $questionId): ?SurveyAnswer
    {
        return SurveyAnswer::where('response_id', $responseId)
            ->where('question_id', $questionId)
            ->first();
    }

    public function deleteByResponse(int $responseId): void
    {
        SurveyAnswer::where('response_id', $responseId)->delete();
    }
}
