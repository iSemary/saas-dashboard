<?php

return [
    'name' => 'FileManager',

    /*
    |--------------------------------------------------------------------------
    | Media Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the centric media upload API endpoints.
    | Size limits are in kilobytes (KB).
    |
    */
    'media' => [
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif', 'bmp', 'tiff', 'svg',
            'mp4', 'mov', 'webm', 'm4v', '3gp',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'zip',
        ],
        'max_photo_size' => env('MEDIA_MAX_PHOTO_SIZE', 10240),     // 10 MB in KB
        'max_video_size' => env('MEDIA_MAX_VIDEO_SIZE', 51200),     // 50 MB in KB
        'max_document_size' => env('MEDIA_MAX_DOCUMENT_SIZE', 20480), // 20 MB in KB
        'max_bulk_count' => env('MEDIA_MAX_BULK_COUNT', 10),        // Max files per bulk upload
    ],
];
