<?php

namespace Modules\FileManager\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Http\UploadedFile;

class AWSService
{
    private $s3Client;
    private $bucket;

    public function __construct()
    {
        $this->bucket = env('AWS_BUCKET');

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false)
        ]);
    }

    /**
     * Upload a file to S3
     * 
     * @param UploadedFile $file
     * @param string $path
     * @param string $visibility
     * @return array|false
     */
    public function upload(UploadedFile $file, string $path = '', string $visibility = 'public')
    {
        try {
            // Generate unique filename
            $filename = $path . '/' . $file->hashName();

            // Upload to S3
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $filename,
                'Body'   => fopen($file->getRealPath(), 'rb'),
                'ACL'    => $visibility === 'public' ? 'public-read' : 'private',
                'ContentType' => $file->getMimeType()
            ]);

            app('log')->info("Success uploading to S3: " . $filename, [
                'bucket' => $this->bucket,
                'key' => $filename,
                'url' => $result['ObjectURL'],
                'visibility' => $visibility
            ]);

            return [
                'success' => true,
                'path' => $filename,
                'url' => $result['ObjectURL'],
                'message' => 'File uploaded successfully'
            ];
        } catch (AwsException $e) {

            app('log')->error("Failed uploading to S3: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'path' => null,
                'url' => null
            ];
        }
    }

    /**
     * Delete a file from S3
     * 
     * @param string $path
     * @return array
     */
    public function delete(string $path): array
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $path
            ]);

            return [
                'success' => true,
                'message' => 'File deleted successfully'
            ];
        } catch (AwsException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get a temporary signed URL for a private file
     * 
     * @param string $path
     * @param int $minutes
     * @return string|false
     */
    public function getSignedUrl(string $path, int $minutes = 5)
    {
        try {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key'    => $path
            ]);

            $request = $this->s3Client->createPresignedRequest($cmd, "+{$minutes} minutes");
            return (string) $request->getUri();
        } catch (AwsException $e) {
            return false;
        }
    }
}
