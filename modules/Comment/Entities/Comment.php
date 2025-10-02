<?php

namespace Modules\Comment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class Comment extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    public $singleTitle = "comment";
    public $pluralTitle = "comments";

    protected $fillable = [
        'parent_id',
        'comment',
        'user_id',
        'seen',
        'object_id',
        'object_model',
        'metadata'
    ];

    protected $casts = [
        'seen' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Get the parent comment
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the child comments (replies)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get all nested replies recursively
     */
    public function allReplies(): HasMany
    {
        return $this->replies()->with('allReplies');
    }

    /**
     * Get the user who made the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the commentable model (polymorphic)
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo('object', 'object_model', 'object_id');
    }

    /**
     * Get the comment attachments
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(CommentAttachment::class);
    }

    /**
     * Get the comment reactions
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Get reactions grouped by type
     */
    public function reactionCounts()
    {
        return $this->reactions()
            ->selectRaw('reaction_type, COUNT(*) as count')
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type');
    }

    /**
     * Check if user has reacted to this comment
     */
    public function hasUserReacted($userId, $reactionType = null)
    {
        $query = $this->reactions()->where('user_id', $userId);
        
        if ($reactionType) {
            $query->where('reaction_type', $reactionType);
        }
        
        return $query->exists();
    }

    /**
     * Get user's reaction to this comment
     */
    public function getUserReaction($userId)
    {
        return $this->reactions()->where('user_id', $userId)->first();
    }

    /**
     * Mark comment as seen by user
     */
    public function markAsSeen()
    {
        $this->update(['seen' => true]);
    }

    /**
     * Check if comment is a reply
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get the root comment (top-level parent)
     */
    public function getRootComment()
    {
        if (!$this->isReply()) {
            return $this;
        }

        $comment = $this;
        while ($comment->parent) {
            $comment = $comment->parent;
        }

        return $comment;
    }

    /**
     * Scope for top-level comments only
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for comments by object
     */
    public function scopeForObject($query, $objectId, $objectModel)
    {
        return $query->where('object_id', $objectId)
                    ->where('object_model', $objectModel);
    }

    /**
     * Scope for unseen comments
     */
    public function scopeUnseen($query)
    {
        return $query->where('seen', false);
    }

    /**
     * Get formatted comment content (strip tags for preview)
     */
    public function getExcerptAttribute($length = 100)
    {
        return \Str::limit(strip_tags($this->comment), $length);
    }
}
