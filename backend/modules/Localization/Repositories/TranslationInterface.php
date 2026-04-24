<?php

namespace Modules\Localization\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TranslationInterface
{
    public function all();
    public function getByKey($key, $attributes = [], $locale = null);
    public function datatables();
    public function find($id);
    public function findOrFail(int $id);
    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator;
    public function exists($key);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function restore($id);
    public function syncMissing();
    public function generateJson();
    public function countJsonByLocale($locale);
    public function countDatatablesJsonByLocale($locale);
    public function updateObjectTranslations(string $decryptedObjectType, string $decryptedObjectKey, int $objectId, array $translations);
    public function getUsedTranslationInJs();
    public function getUsedTranslationInPhp();
    public function syncJsonFiles();
}

