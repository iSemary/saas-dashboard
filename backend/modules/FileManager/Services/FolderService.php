<?php

namespace Modules\FileManager\Services;

use Modules\FileManager\DTOs\CreateFolderData;
use Modules\FileManager\DTOs\UpdateFolderData;
use Modules\FileManager\Entities\Folder;
use Modules\FileManager\Repositories\FileInterface;

class FolderService
{
    public function __construct(protected FileInterface $repository) {}

    public function list(array $filters = [])
    {
        $query = Folder::with(['files', 'parent']);

        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        } else {
            $query->whereNull('parent_id');
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('name')->get();
    }

    public function findOrFail(int $id): Folder
    {
        return Folder::with(['files', 'parent'])->findOrFail($id);
    }

    public function create(CreateFolderData $data): Folder
    {
        $arrayData = [
            'name' => $data->name,
            'description' => $data->description,
            'parent_id' => $data->parent_id,
            'status' => $data->status ?? 'active',
        ];
        return Folder::create($arrayData);
    }

    public function update(int $id, UpdateFolderData $data): Folder
    {
        $folder = Folder::findOrFail($id);
        $folder->update($data->toArray());
        return $folder->load(['files', 'parent']);
    }

    public function delete(int $id): bool
    {
        $folder = Folder::findOrFail($id);

        if ($folder->files()->count() > 0) {
            throw new \Exception(translate('exception.cannot_delete_with_associated'));
        }

        if (Folder::where('parent_id', $id)->count() > 0) {
            throw new \Exception('Cannot delete folder with subfolders');
        }

        return $folder->delete();
    }
}
