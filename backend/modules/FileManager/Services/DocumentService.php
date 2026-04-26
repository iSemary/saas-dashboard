<?php

namespace Modules\FileManager\Services;

use Modules\FileManager\Entities\File;
use Modules\FileManager\Entities\Folder;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function list(array $filters = [], int $perPage = 20)
    {
        $query = File::with('folder');

        if (isset($filters['folder_id'])) {
            $query->where('folder_id', $filters['folder_id']);
        } else {
            $query->whereNull('folder_id');
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->where('original_name', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): File
    {
        return File::with('folder')->findOrFail($id);
    }

    public function upload(array $data, $uploadedFile): File
    {
        $folderId = $data['folder_id'] ?? null;
        $accessLevel = $data['access_level'] ?? 'public';

        $hashName = $uploadedFile->hashName();
        $originalName = $uploadedFile->getClientOriginalName();
        $mimeType = $uploadedFile->getMimeType();
        $size = $uploadedFile->getSize();
        $checksum = hash_file('sha256', $uploadedFile->getRealPath());

        $folderPath = $folderId ? Folder::find($folderId)->name : 'documents';
        $uploadedFile->storeAs($folderPath, $hashName, 'public');

        return File::create([
            'folder_id' => $folderId,
            'hash_name' => $hashName,
            'checksum' => $checksum,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'host' => 'local',
            'status' => 'active',
            'access_level' => $accessLevel,
            'size' => $size,
            'metadata' => [
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now()->toIso8601String(),
            ],
            'is_encrypted' => false,
        ]);
    }

    public function update(int $id, array $data): File
    {
        $file = File::findOrFail($id);
        $file->update($data);
        return $file->load('folder');
    }

    public function delete(int $id): bool
    {
        $file = File::findOrFail($id);
        $folderPath = $file->folder ? $file->folder->name : 'documents';
        Storage::disk('public')->delete("{$folderPath}/{$file->hash_name}");
        return $file->delete();
    }

    public function bulkDelete(array $ids): int
    {
        $files = File::whereIn('id', $ids)->get();
        $deleted = 0;

        foreach ($files as $file) {
            $folderPath = $file->folder ? $file->folder->name : 'documents';
            Storage::disk('public')->delete("{$folderPath}/{$file->hash_name}");
            $file->delete();
            $deleted++;
        }

        return $deleted;
    }

    public function download(int $id)
    {
        $file = File::findOrFail($id);

        $user = auth()->user();
        if ($file->access_level === 'private' && $file->metadata && isset($file->metadata['uploaded_by'])) {
            if ($file->metadata['uploaded_by'] != $user->id) {
                throw new \Exception(translate('auth.unauthorized'));
            }
        }

        $folderPath = $file->folder ? $file->folder->name : 'documents';
        $filePath = "{$folderPath}/{$file->hash_name}";

        if (!Storage::disk('public')->exists($filePath)) {
            throw new \Exception(translate('message.file_not_found'));
        }

        return Storage::disk('public')->path($filePath);
    }
}
