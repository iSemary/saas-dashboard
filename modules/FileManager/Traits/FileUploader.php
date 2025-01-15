<?php

namespace Modules\FileManager\Traits;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\FileManager\Entities\File;

trait ImageUploader
{
    /**
     * Specify the columns that store media information.
     * For example, ['icon', 'header_image']
     */
    protected $imageColumns = [];

    /**
     * Handle image uploads automatically and store the hash ID in the model's column.
     *
     * @param  array  $files
     * @return void
     */
    public static function bootImageUploader()
    {
        static::saving(function ($model) {
            // Loop through the defined image columns
            foreach ($model->imageColumns as $column) {
                if ($model->{$column} instanceof UploadedFile) {
                    $model->{$column . '_id'} = $model->uploadImage($model->{$column});
                    $model->{$column} = null;  // Optionally clear the image file after storing the hash ID
                }
            }
        });
    }

    /**
     * Upload the image to the storage and save metadata in the `media` table.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $disk
     * @return int  The hash ID of the uploaded file
     */
    public function uploadImage(UploadedFile $file, string $disk = 'public'): int
    {
        // Generate unique hash name
        $hashName = $file->hashName();

        // Store the image
        $filePath = $file->storeAs('images', $hashName, $disk);

        // Create media record
        $media = File::create([
            'hash_name'     => $hashName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'server'        => $disk,
        ]);

        return $media->id;
    }

    /**
     * Retrieve the URL for an image by its hash ID.
     *
     * @param  int  $mediaId
     * @param  string  $disk
     * @return string|null
     */
    public function getImageUrl(int $mediaId, string $disk = 'public'): ?string
    {
        $media = File::find($mediaId);

        return $media ? Storage::disk($disk)->url("images/{$media->hash_name}") : null;
    }
}
