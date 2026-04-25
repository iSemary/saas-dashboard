<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyQuestion;

interface SurveyQuestionRepositoryInterface
{
    public function find(int $id): ?SurveyQuestion;
    public function findOrFail(int $id): SurveyQuestion;
    public function create(array $data): SurveyQuestion;
    public function update(int $id, array $data): SurveyQuestion;
    public function delete(int $id): bool;
    public function findBySurvey(int $surveyId): array;
    public function findByPage(int $pageId): array;
    public function reorder(int $surveyId, array $orderedIds): void;
    public function getNextQuestionOrder(int $pageId): int;
    public function findNextQuestion(SurveyQuestion $current): ?SurveyQuestion;
    public function findFirstOfPage(int $pageId): ?SurveyQuestion;
    public function getScorableQuestions(int $surveyId): array;
}
