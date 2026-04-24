<?php

namespace Modules\FileManager\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\FileManager\Entities\File;
use Modules\FileManager\Entities\Folder;

class MediaService
{
    private $awsService;

    /** Mime type to storage extension (photos, videos, documents, including iOS). */
    private const MIME_TO_EXTENSION = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/heic' => 'heic',
        'image/heif' => 'heif',
        'image/bmp' => 'bmp',
        'image/tiff' => 'tiff',
        'image/svg+xml' => 'svg',
        'video/mp4' => 'mp4',
        'video/quicktime' => 'mov',
        'video/webm' => 'webm',
        'video/x-m4v' => 'm4v',
        'video/3gpp' => '3gp',
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'text/csv' => 'csv',
        'text/plain' => 'txt',
        'application/zip' => 'zip',
    ];

    private const IMAGE_MIME_PREFIX = 'image/';
    private const VIDEO_MIME_PREFIX = 'video/';
    private const DOCUMENT_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
        'text/plain',
        'application/zip',
    ];

    public function __construct(AWSService $awsService)
    {
        $this->awsService = $awsService;
    }

    /**
     * Upload a single file and create a media record.
     */
    public function upload(UploadedFile $file, ?int $folderId = null, string $accessLevel = 'public'): File
    {
        $this->validateFile($file);

        $mimeType = $file->getMimeType();
        $sizeBytes = $file->getSize();
        $originalName = $file->getClientOriginalName();
        $host = $this->getCurrentHost();

        // Generate storage key: {folder_name}/{year}/{month}/{uuid}.{ext}
        $folderName = $folderId ? Folder::find($folderId)?->name ?? 'media' : 'media';
        $extension = $this->getExtensionFromMime($mimeType);
        $storageKey = $folderName . '/' . date('Y') . '/' . date('m') . '/' . Str::uuid() . '.' . $extension;

        // Store file
        $url = $this->storeFile($file, $storageKey, $host, $accessLevel);

        // Create media record
        $media = File::create([
            'folder_id' => $folderId,
            'hash_name' => basename($storageKey),
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'host' => $host,
            'status' => 'active',
            'access_level' => $accessLevel,
            'size' => $sizeBytes,
            'metadata' => [
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now()->toIso8601String(),
                'storage_key' => $storageKey,
            ],
            'is_encrypted' => false,
        ]);

        // Attach URL to the model for response
        $media->url = $url;

        return $media;
    }

    /**
     * Upload multiple files in bulk.
     *
     * @param UploadedFile[] $files
     * @return File[]
     */
    public function uploadBulk(array $files, ?int $folderId = null, string $accessLevel = 'public'): array
    {
        $results = [];
        foreach ($files as $file) {
            $results[] = $this->upload($file, $folderId, $accessLevel);
        }
        return $results;
    }

    /**
     * Find a media record by ID and attach its URL.
     */
    public function findById(int $id): File
    {
        $media = File::with('folder')->findOrFail($id);
        $media->url = $this->getUrl($media);
        return $media;
    }

    /**
     * Delete a media record and its physical file.
     */
    public function delete(int $id): void
    {
        $media = File::findOrFail($id);

        // Delete physical file
        $this->deletePhysicalFile($media);

        $media->delete();
    }

    /**
     * Get the URL for a media record based on its host.
     */
    public function getUrl(File $file): string
    {
        $storageKey = $file->metadata['storage_key'] ?? null;

        if (!$storageKey) {
            // Fallback to legacy path construction
            $folderName = $file->folder ? $file->folder->name : 'documents';
            $storageKey = $folderName . '/' . $file->hash_name;
        }

        if ($file->host === 'aws') {
            return $this->awsService->getSignedUrl($storageKey) ?: Storage::disk('s3')->url($storageKey);
        }

        if ($file->access_level === 'public') {
            return asset('storage/' . $storageKey);
        }

        return Storage::disk($file->access_level)->url($storageKey);
    }

    /**
     * Validate the uploaded file (mime type, extension, size).
     */
    public function validateFile(UploadedFile $file): void
    {
        $mimeType = $file->getMimeType();
        $originalName = $file->getClientOriginalName();
        $sizeBytes = $file->getSize();

        // Validate mime type
        $acceptedExtensions = $this->getAcceptedExtensionsForMime($mimeType);
        if (empty($acceptedExtensions)) {
            $allowed = config('filemanager.media.allowed_extensions', []);
            throw new \InvalidArgumentException(
                "Unsupported file type. Allowed extensions: " . implode(', ', $allowed) . ". " .
                "Only photo, video, and document formats are accepted (e.g. jpg, png, pdf, docx)."
            );
        }

        // Validate extension against allowlist
        $extFromName = $this->getExtensionFromFilename($originalName);
        $allowed = config('filemanager.media.allowed_extensions', []);
        $extToCheck = $extFromName ?? $acceptedExtensions[0];

        if (!empty($allowed) && !in_array($extToCheck, $allowed)) {
            throw new \InvalidArgumentException(
                "File extension \".{$extToCheck}\" is not allowed. " .
                "Allowed extensions: " . implode(', ', $allowed) . "."
            );
        }

        // Validate extension matches mime type
        if ($extFromName && !in_array($extFromName, $acceptedExtensions)) {
            throw new \InvalidArgumentException(
                "File extension \".{$extFromName}\" does not match the file content ({$mimeType}). " .
                "Use the correct extension for the file type."
            );
        }

        // Validate file size
        $this->validateFileSize($mimeType, $sizeBytes);
    }

    /**
     * Validate file size based on mime type.
     */
    private function validateFileSize(string $mimeType, int $sizeBytes): void
    {
        $maxPhoto = config('filemanager.media.max_photo_size', 10240) * 1024; // KB to bytes
        $maxVideo = config('filemanager.media.max_video_size', 51200) * 1024;
        $maxDocument = config('filemanager.media.max_document_size', 20480) * 1024;
        $formatMb = fn($bytes) => number_format($bytes / (1024 * 1024), 1);

        if (Str::startsWith($mimeType, self::IMAGE_MIME_PREFIX)) {
            if ($sizeBytes > $maxPhoto) {
                throw new \InvalidArgumentException(
                    "Photo size must not exceed {$formatMb($maxPhoto)} MB. Current size: {$formatMb($sizeBytes)} MB."
                );
            }
            return;
        }

        if (Str::startsWith($mimeType, self::VIDEO_MIME_PREFIX)) {
            if ($sizeBytes > $maxVideo) {
                throw new \InvalidArgumentException(
                    "Video size must not exceed {$formatMb($maxVideo)} MB. Current size: {$formatMb($sizeBytes)} MB."
                );
            }
            return;
        }

        if (in_array($mimeType, self::DOCUMENT_MIME_TYPES)) {
            if ($sizeBytes > $maxDocument) {
                throw new \InvalidArgumentException(
                    "Document size must not exceed {$formatMb($maxDocument)} MB. Current size: {$formatMb($sizeBytes)} MB."
                );
            }
            return;
        }

        // Fallback
        if ($sizeBytes > max($maxPhoto, $maxVideo)) {
            throw new \InvalidArgumentException('File is too large.');
        }
    }

    /**
     * Store the file in the appropriate storage.
     */
    private function storeFile(UploadedFile $file, string $storageKey, string $host, string $accessLevel): string
    {
        if ($host === 'aws') {
            $result = $this->awsService->upload($file, dirname($storageKey), $accessLevel);
            if (!$result['success']) {
                throw new \RuntimeException('Failed to upload file to AWS: ' . $result['message']);
            }
            return $result['url'];
        }

        // Local storage
        $directory = dirname($storageKey);
        $filename = basename($storageKey);
        $filePath = $file->storeAs($directory, $filename, 'public');

        if (!$filePath) {
            throw new \RuntimeException('Failed to store the file.');
        }

        return Storage::disk('public')->url($filePath);
    }

    /**
     * Delete the physical file from storage.
     */
    private function deletePhysicalFile(File $file): void
    {
        $storageKey = $file->metadata['storage_key'] ?? null;

        if ($file->host === 'aws') {
            if ($storageKey) {
                $this->awsService->delete($storageKey);
            }
            return;
        }

        // Local storage
        if ($storageKey) {
            Storage::disk('public')->delete($storageKey);
        } else {
            // Legacy path
            $folderName = $file->folder ? $file->folder->name : 'documents';
            Storage::disk('public')->delete("{$folderName}/{$file->hash_name}");
        }
    }

    /**
     * Get the current storage host (aws or local).
     */
    private function getCurrentHost(): string
    {
        try {
            $configurationService = app('Modules\Development\Services\ConfigurationService');
            return $configurationService->getByKey('file_manager.default_host') ?? 'local';
        } catch (\Exception $e) {
            return 'local';
        }
    }

    /**
     * Get extension from mime type mapping.
     */
    private function getExtensionFromMime(string $mimeType): string
    {
        return self::MIME_TO_EXTENSION[$mimeType] ?? 'bin';
    }

    /**
     * Get accepted extensions for a given mime type.
     */
    private function getAcceptedExtensionsForMime(string $mimeType): array
    {
        $ext = self::MIME_TO_EXTENSION[$mimeType] ?? null;
        if (!$ext) {
            return [];
        }
        if ($mimeType === 'image/jpeg') return ['jpg', 'jpeg'];
        if ($mimeType === 'image/tiff') return ['tiff', 'tif'];
        return [$ext];
    }

    /**
     * Extract extension from a filename.
     */
    private function getExtensionFromFilename(?string $filename): ?string
    {
        if (!$filename || !trim($filename)) return null;
        $parts = explode('.', trim($filename));
        if (count($parts) < 2) return null;
        return strtolower(array_pop($parts));
    }
}
