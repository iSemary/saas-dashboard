<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyTemplate;

class EloquentSurveyTemplateRepository implements SurveyTemplateRepositoryInterface
{
    public function find(int $id): ?SurveyTemplate
    {
        return SurveyTemplate::find($id);
    }

    public function findOrFail(int $id): SurveyTemplate
    {
        return SurveyTemplate::findOrFail($id);
    }

    public function create(array $data): SurveyTemplate
    {
        return SurveyTemplate::create($data);
    }

    public function update(int $id, array $data): SurveyTemplate
    {
        $template = $this->findOrFail($id);
        $template->update($data);
        return $template->fresh();
    }

    public function delete(int $id): bool
    {
        $template = $this->find($id);
        return $template ? $template->delete() : false;
    }

    public function list(array $filters = []): array
    {
        $query = SurveyTemplate::query();

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['is_system'])) {
            $query->where('is_system', $filters['is_system']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('name')->get()->toArray();
    }

    public function findByCategory(string $category): array
    {
        return SurveyTemplate::where('category', $category)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getSystemTemplates(): array
    {
        return SurveyTemplate::where('is_system', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
