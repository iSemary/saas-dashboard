<?php

namespace Modules\Comment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class CommentAttachment extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    public $singleTitle = "comment_attachment";
    public $pluralTitle = "comment_attachments";

    protected $fillable = [
        'comment_id',
        'attachment_url',
        'thumbnail_url',
        'original_name',
        'mime_type',
        'file_size',
        'user_id',
        'metadata'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'metadata' => 'array'
    ];

    protected $fileColumns = [
        'attachment_url' => [
            'folder' => 'comments/attachments',
            'is_encrypted' => false,
            'access_level' => 'private',
            'metadata' => ['width', 'height', 'duration'],
        ],
        'thumbnail_url' => [
            'folder' => 'comments/thumbnails',
            'is_encrypted' => false,
            'access_level' => 'private',
            'metadata' => ['width', 'height'],
        ],
    ];

    /**
     * Get the comment this attachment belongs to
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the user who uploaded the attachment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the attachment URL dynamically
     */
    public function getAttachmentUrlAttribute($value)
    {
        return $this->getFileUrl($value);
    }

    /**
     * Get the thumbnail URL dynamically
     */
    public function getThumbnailUrlAttribute($value)
    {
        return $value ? $this->getFileUrl($value) : null;
    }

    /**
     * Set the attachment URL attribute
     */
    public function setAttachmentUrlAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $media = $this->upload($value, 'attachment_url');
            $this->attributes['attachment_url'] = $media->id;
            
            // Store file metadata
            $this->attributes['original_name'] = $value->getClientOriginalName();
            $this->attributes['mime_type'] = $value->getMimeType();
            $this->attributes['file_size'] = $value->getSize();
            
            // Generate thumbnail for images
            if ($this->isImage($value->getMimeType())) {
                $this->generateThumbnail($media);
            }
        } else {
            $this->attributes['attachment_url'] = $value;
        }
    }

    /**
     * Set the thumbnail URL attribute
     */
    public function setThumbnailUrlAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $media = $this->upload($value, 'thumbnail_url');
            $this->attributes['thumbnail_url'] = $media->id;
        } else {
            $this->attributes['thumbnail_url'] = $value;
        }
    }

    /**
     * Check if file is an image
     */
    public function isImage($mimeType = null): bool
    {
        $mimeType = $mimeType ?: $this->mime_type;
        return str_starts_with($mimeType, 'image/');
    }

    /**
     * Check if file is a video
     */
    public function isVideo($mimeType = null): bool
    {
        $mimeType = $mimeType ?: $this->mime_type;
        return str_starts_with($mimeType, 'video/');
    }

    /**
     * Check if file is a document
     */
    public function isDocument($mimeType = null): bool
    {
        $mimeType = $mimeType ?: $this->mime_type;
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv'
        ];
        
        return in_array($mimeType, $documentTypes);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    /**
     * Generate thumbnail for image files
     */
    protected function generateThumbnail($media)
    {
        try {
            // This would integrate with your existing image processing service
            // For now, we'll store the reference to generate later
            $this->attributes['thumbnail_url'] = $media->id;
        } catch (\Exception $e) {
            \Log::error('Failed to generate thumbnail: ' . $e->getMessage());
        }
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope for documents only
     */
    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv'
        ]);
    }
}
