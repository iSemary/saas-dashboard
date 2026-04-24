<?php

namespace App\Helpers;

use Modules\FileManager\Entities\File;
use Modules\FileManager\Entities\Folder;
use Illuminate\Http\UploadedFile;
use Storage;

class FileHelper
{
    protected $configurationService;
    protected $AWSService;

    public function __construct()
    {
        $this->configurationService = app('Modules\Development\Services\ConfigurationService');
        $this->AWSService = app('Modules\FileManager\Services\AWSService');
    }
    public static function returnSizeString(int $size = null)
    {
        if ($size === null) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($size, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function directUpload(UploadedFile $file, string $folderName)
    {
        try {
            $hashName = $file->hashName();
            $host = $this->getCurrentHost();

            // Handle file storage
            $uploadResult = $this->storeFile($file, $folderName, $hashName, 'public', $host);

            // Create and return media record
            return $this->createMediaRecord($file, $hashName, $uploadResult, $host, 'public', $folderName);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function createMediaRecord(
        UploadedFile $file,
        string $hashName,
        array $uploadResult,
        string $host,
        string $disk,
        string $folderName,
    ) {
        return File::create([
            'folder_id' => $this->getOrCreateFolder($folderName)->id,
            'hash_name' => $hashName,
            'checksum' => $this->generateChecksum($file, $uploadResult['path'], $host, $disk),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'host' => $host,
            'status' => 'active',
            'access_level' => 'public',
            'size' => $file->getSize(),
            'metadata' => json_encode([]),
            'is_encrypted' => false,
            'url' => $uploadResult['url'],
        ]);
    }

    private function getOrCreateFolder(string $name, ?int $parentId = null): Folder
    {
        return Folder::firstOrCreate(
            [
                'name' => $name,
                'parent_id' => $parentId,
            ],
            [
                'description' => 'Auto-generated folder for storing images',
                'status' => 'active',
            ]
        );
    }

    /**
     * Retrieve the current host for the file. [aws, local, azure, etc.]
     *
     * @return string
     */
    public function getCurrentHost(): string
    {
        return $this->configurationService->getByKey('file_manager.default_host');
    }

    /**
     * Generate checksum for the file
     */
    private function generateChecksum(UploadedFile $file, string $filePath, string $host, string $disk): ?string
    {
        if ($host === 'aws') {
            return $file->get() ? md5($file->get()) : null;
        }
        return md5_file(Storage::disk($disk)->path($filePath));
    }

    /**
     * Store the file in the appropriate storage
     */
    private function storeFile(UploadedFile $file, string $folderName, string $hashName, string $disk, string $host): array
    {
        switch ($host) {
            case 'local':
                return $this->storeFileLocally($file, $folderName, $hashName, $disk);
            case 'aws':
                return $this->storeFileOnAWS($file, $folderName);
            default:
                throw new \Exception("Unsupported host: $host");
        }
    }

    /**
     * Store file in local storage
     */
    private function storeFileLocally(UploadedFile $file, string $folderName, string $hashName, string $disk): array
    {
        $filePath = $file->storeAs($folderName, $hashName, $disk);
        if (!$filePath) {
            throw new \Exception("Failed to store the file.");
        }
        return [
            'path' => $filePath,
            'url' => Storage::disk($disk)->url($filePath)
        ];
    }

    /**
     * Store file in AWS storage
     */
    private function storeFileOnAWS(UploadedFile $file, string $folderName): array
    {
        return $this->AWSService->upload(
            $file,
            $folderName,
            'public'
        );
    }
}
