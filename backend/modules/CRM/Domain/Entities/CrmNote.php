<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

/**
 * Polymorphic note that can be attached to any CRM record.
 */
class CrmNote extends Model
{
    use SoftDeletes;

    protected $table = 'crm_notes';

    protected $fillable = [
        'content',
        'related_type',
        'related_id',
        'created_by',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the related entity (polymorphic).
     */
    public function related(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created this note.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get notes for a specific related entity.
     */
    public function scopeForRelated($query, string $type, int $id)
    {
        return $query->where('related_type', $type)
            ->where('related_id', $id);
    }

    /**
     * Business method: Edit note content.
     */
    public function edit(string $newContent): void
    {
        $this->update(['content' => $newContent]);
    }

    /**
     * Get a truncated preview of the note content.
     */
    public function preview(int $length = 100): string
    {
        if (strlen($this->content) <= $length) {
            return $this->content;
        }

        return substr($this->content, 0, $length) . '...';
    }

    /**
     * Get related entity type label.
     */
    public function relatedTypeLabel(): string
    {
        $class = class_basename($this->related_type);

        return match ($class) {
            'Lead' => 'Lead',
            'Contact' => 'Contact',
            'Company' => 'Company',
            'Opportunity' => 'Opportunity',
            default => $class,
        };
    }
}
