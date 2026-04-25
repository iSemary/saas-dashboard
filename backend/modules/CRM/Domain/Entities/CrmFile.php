<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Entities\User;

/**
 * File attachment that can be linked to any CRM record.
 */
class CrmFile extends Model
{
    protected $table = 'crm_files';

    protected $fillable = [
        'filename',
        'path',
        'mime_type',
        'size',
        'related_type',
        'related_id',
        'created_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Get the related entity (polymorphic).
     */
    public function related(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this file.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get files for a specific related entity.
     */
    public function scopeForRelated($query, string $type, int $id)
    {
        return $query->where('related_type', $type)
            ->where('related_id', $id);
    }

    /**
     * Business method: Delete file from storage and database.
     */
    public function remove(): void
    {
        // Delete from storage
        if (Storage::exists($this->path)) {
            Storage::delete($this->path);
        }

        // Delete from database
        $this->delete();
    }

    /**
     * Get file URL for download.
     */
    public function getUrl(): string
    {
        return Storage::url($this->path);
    }

    /**
     * Get formatted file size.
     */
    public function formattedSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get file extension.
     */
    public function extension(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }
}
