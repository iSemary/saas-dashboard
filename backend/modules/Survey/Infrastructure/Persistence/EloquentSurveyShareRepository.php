<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyShare;

class EloquentSurveyShareRepository implements SurveyShareRepositoryInterface
{
    public function find(int $id): ?SurveyShare
    {
        return SurveyShare::find($id);
    }

    public function findOrFail(int $id): SurveyShare
    {
        return SurveyShare::findOrFail($id);
    }

    public function findByToken(string $token): ?SurveyShare
    {
        return SurveyShare::where('token', $token)->first();
    }

    public function create(array $data): SurveyShare
    {
        return SurveyShare::create($data);
    }

    public function update(int $id, array $data): SurveyShare
    {
        $share = $this->findOrFail($id);
        $share->update($data);
        return $share->fresh();
    }

    public function delete(int $id): bool
    {
        $share = $this->find($id);
        return $share ? $share->delete() : false;
    }

    public function findBySurvey(int $surveyId): array
    {
        return SurveyShare::where('survey_id', $surveyId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function findBySurveyAndChannel(int $surveyId, string $channel): array
    {
        return SurveyShare::where('survey_id', $surveyId)
            ->where('channel', $channel)
            ->get()
            ->toArray();
    }

    public function incrementUses(int $id): void
    {
        SurveyShare::where('id', $id)->increment('uses_count');
    }
}
