<?php

declare(strict_types=1);

namespace Modules\Survey\Infrastructure\Persistence;

use Modules\Survey\Domain\Entities\SurveyTheme;

class EloquentSurveyThemeRepository implements SurveyThemeRepositoryInterface
{
    public function find(int $id): ?SurveyTheme
    {
        return SurveyTheme::find($id);
    }

    public function findOrFail(int $id): SurveyTheme
    {
        return SurveyTheme::findOrFail($id);
    }

    public function create(array $data): SurveyTheme
    {
        return SurveyTheme::create($data);
    }

    public function update(int $id, array $data): SurveyTheme
    {
        $theme = $this->findOrFail($id);
        $theme->update($data);
        return $theme->fresh();
    }

    public function delete(int $id): bool
    {
        $theme = $this->find($id);
        return $theme ? $theme->delete() : false;
    }

    public function list(): array
    {
        return SurveyTheme::orderBy('name')->get()->toArray();
    }

    public function getSystemThemes(): array
    {
        return SurveyTheme::where('is_system', true)
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
