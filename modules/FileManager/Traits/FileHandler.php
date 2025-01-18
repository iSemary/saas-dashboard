<?php

namespace Modules\FileManager\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\FileManager\Entities\File;
use Modules\FileManager\Entities\Folder;

trait FileHandler
{
    protected $configurationService;
    protected $AWSService;

    /**
     * Initialize required services
     */
    public function __construct()
    {
        parent::__construct();
        $this->configurationService = app('Modules\Development\Services\ConfigurationService');
        $this->AWSService = app('Modules\FileManager\Services\AWSService');
    }

    /**
     * Handle image uploads automatically and store the hash ID in the model's column.
     *
     * @return void
     */
    public static function bootImageUploader()
    {
        static::saving(function ($model) {
            foreach ($model->fileColumns as $column) {
                if ($model->{$column} instanceof UploadedFile) {
                    $model->{$column . '_id'} = $model->uploadImage($model->{$column}, $column);
                    $model->{$column} = null;
                }
            }
        });
    }

    /**
     * Upload the image to the storage and save metadata in the `media` table.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $column
     * @return File|string  The uploaded file instance or error message
     */
    public function upload(UploadedFile $file, string $column = 'icon')
    {
        try {

            $hashName = $file->hashName();
            $disk = $this->getDiskFromColumn($column);
            $host = $this->getCurrentHost();
            $folderName = $this->getFolderNameFromColumn($column);

            // Handle file storage
            $uploadResult = $this->storeFile($file, $folderName, $hashName, $disk, $host, $column);

            // Get metadata
            $fileMetadata = $this->collectMetadata($file, $uploadResult['path'], $disk, $host, $column);

            // Create and return media record
            return $this->createMediaRecord($file, $hashName, $uploadResult, $host, $column, $fileMetadata, $disk);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get the appropriate disk based on column configuration
     */
    private function getDiskFromColumn(string $column): string
    {
        return $this->fileColumns[$column]['access_level'] ?? 'public';
    }

    /**
     * Get folder name from column configuration
     */
    private function getFolderNameFromColumn(string $column): string
    {
        return $this->fileColumns[$column]['folder'] ?? 'files';
    }

    /**
     * Store the file in the appropriate storage
     */
    private function storeFile(UploadedFile $file, string $folderName, string $hashName, string $disk, string $host, string $column): array
    {
        switch ($host) {
            case 'local':
                return $this->storeFileLocally($file, $folderName, $hashName, $disk);
            case 'aws':
                return $this->storeFileOnAWS($file, $folderName, $column);
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
    private function storeFileOnAWS(UploadedFile $file, string $folderName, string $column): array
    {
        return $this->AWSService->upload(
            $file,
            $folderName,
            $this->fileColumns[$column]['access_level'] ?? 'public'
        );
    }

    /**
     * Get image dimensions based on host
     */
    private function getImageDimensions(string $filePath, string $disk, string $host): ?array
    {
        if ($host === 'aws') {
            return $this->getAWSImageDimensions($filePath, $disk);
        }
        return $this->getLocalImageDimensions($filePath, $disk);
    }

    /**
     * Get dimensions for AWS-stored images
     */
    private function getAWSImageDimensions(string $filePath, string $disk): ?array
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'aws_image');
        $fileContent = Storage::disk($disk)->get($filePath);
        if (!$fileContent) {
            return null;
        }

        file_put_contents($tempFilePath, $fileContent);
        $dimensions = getimagesize($tempFilePath);
        unlink($tempFilePath);

        return $dimensions;
    }

    /**
     * Get dimensions for locally stored images
     */
    private function getLocalImageDimensions(string $filePath, string $disk): ?array
    {
        return getimagesize(Storage::disk($disk)->path($filePath));
    }

    /**
     * Collect metadata for the file
     */
    private function collectMetadata(UploadedFile $file, string $filePath, string $disk, string $host, string $column): array
    {
        $fileMetadata = [];
        if (empty($this->fileColumns[$column]['metadata'])) {
            return $fileMetadata;
        }

        foreach ($this->fileColumns[$column]['metadata'] as $metaKey) {
            $fileMetadata = array_merge(
                $fileMetadata,
                $this->getMetadataForKey($metaKey, $file, $filePath, $disk, $host)
            );
        }

        return $fileMetadata;
    }

    /**
     * Get specific metadata based on key
     */
    private function getMetadataForKey(string $metaKey, UploadedFile $file, string $filePath, string $disk, string $host): array
    {
        switch ($metaKey) {
            case 'width':
            case 'height':
                $dimensions = $this->getImageDimensions($filePath, $disk, $host);
                return [
                    'width' => $dimensions[0] ?? null,
                    'height' => $dimensions[1] ?? null
                ];

            case 'size':
                return ['size' => $file->getSize()];

            case 'mime_type':
                return ['mime_type' => $file->getMimeType()];

            case 'aspect_ratio':
                $dimensions = $this->getImageDimensions($filePath, $disk, $host);
                if (!empty($dimensions[0]) && !empty($dimensions[1])) {
                    return ['aspect_ratio' => $dimensions[0] / $dimensions[1]];
                }
                return [];

            default:
                return [];
        }
    }

    /**
     * Create the media record in the database
     */
    private function createMediaRecord(
        UploadedFile $file,
        string $hashName,
        array $uploadResult,
        string $host,
        string $column,
        array $fileMetadata,
        string $disk
    ): File {
        return File::create([
            'folder_id' => $this->getFolderIdFromColumn($column),
            'hash_name' => $hashName,
            'checksum' => $this->generateChecksum($file, $uploadResult['path'], $host, $disk),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'host' => $host,
            'status' => 'active',
            'access_level' => $this->fileColumns[$column]['access_level'] ?? 'public',
            'size' => $file->getSize(),
            'metadata' => json_encode($fileMetadata),
            'is_encrypted' => $this->fileColumns[$column]['is_encrypted'] ?? false,
            'url' => $uploadResult['url'],
        ]);
    }

    /**
     * Get folder ID based on column configuration
     */
    private function getFolderIdFromColumn(string $column): ?int
    {
        return isset($this->fileColumns[$column]['folder'])
            ? $this->getFolderId($this->fileColumns[$column]['folder'])
            : null;
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
     * Get or create folder ID by name
     *
     * @param string $folderName
     * @param string|null $parentFolderName
     * @return int
     */
    public function getFolderId(string $folderName, $parentFolderName = null): int
    {
        $parentFolderId = null;

        if ($parentFolderName) {
            return $this->getOrCreateFolder($parentFolderName, null)->id;
        }

        return $this->getOrCreateFolder($folderName, $parentFolderId)->id;
    }

    /**
     * Get or create a folder
     */
    private function getOrCreateFolder(string $name, ?int $parentId): Folder
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
     * Get the URL of the file based on the host
     *
     * @return int|null
     */
    public function getFileUrl(int $fileId = null): ?string
    {
        if (!$fileId) {
            return null;
        }
        $file = File::where('id', $fileId)->first();
        if (!$file) {
            return null;
        }

        switch ($file->host) {
            case 'aws':
                return $this->getAWSFileUrl($file);
            case 'local':
                return $this->getLocalFileUrl($file);
            default:
                return null;
        }
    }

    /**
     * Get the AWS S3 file URL for the given file.
     *
     * @param File $file The file object containing information about the file.
     * @return string The URL of the file stored in AWS S3.
     */
    private function getAWSFileUrl(File $file): string
    {
        return Storage::disk('s3')->url("{$file->folder->name}/{$file->hash_name}");
    }

    /**
     * Get the local file URL based on the file's access level.
     *
     * This method generates a URL for accessing a file stored locally. If the file's access level is 'public',
     * it uses the `asset` helper to generate a URL pointing to the public storage path. For other access levels,
     * it uses the `Storage` facade to generate a URL based on the specified disk and file path.
     *
     * @param File $file The file object containing information about the file.
     * @return string The URL to access the file.
     */
    private function getLocalFileUrl(File $file): string
    {
        if ($file->access_level === 'public') {
            if ($file->folder) {
                return asset("storage/{$file->folder->name}/{$file->hash_name}");
            } else {
                return asset("storage/{$file->hash_name}");
            }
        }

        if ($file->folder) {
            return Storage::disk($file->access_level)->url("{$file->folder->name}/{$file->hash_name}");
        }
        return Storage::disk($file->access_level)->url("{$file->hash_name}");
    }
}
