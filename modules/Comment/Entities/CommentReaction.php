<?php

namespace Modules\Comment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class CommentReaction extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public $singleTitle = "comment_reaction";
    public $pluralTitle = "comment_reactions";

    protected $fillable = [
        'comment_id',
        'reaction_type',
        'user_id'
    ];

    /**
     * Available reaction types
     */
    const REACTION_TYPES = [
        'like' => '👍',
        'love' => '❤️',
        'dislike' => '👎',
        'laugh' => '😂',
        'angry' => '😠',
        'sad' => '😢'
    ];

    /**
     * Get the comment this reaction belongs to
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the user who made the reaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the emoji for this reaction type
     */
    public function getEmojiAttribute(): string
    {
        return self::REACTION_TYPES[$this->reaction_type] ?? '👍';
    }

    /**
     * Get all available reaction types
     */
    public static function getReactionTypes(): array
    {
        return self::REACTION_TYPES;
    }

    /**
     * Scope for specific reaction type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('reaction_type', $type);
    }

    /**
     * Scope for reactions by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Toggle reaction for a user on a comment
     */
    public static function toggleReaction($commentId, $userId, $reactionType)
    {
        $existing = self::where('comment_id', $commentId)
                       ->where('user_id', $userId)
                       ->first();

        if ($existing) {
            if ($existing->reaction_type === $reactionType) {
                // Same reaction - remove it
                $existing->delete();
                return null;
            } else {
                // Different reaction - update it
                $existing->update(['reaction_type' => $reactionType]);
                return $existing;
            }
        } else {
            // New reaction - create it
            return self::create([
                'comment_id' => $commentId,
                'user_id' => $userId,
                'reaction_type' => $reactionType
            ]);
        }
    }

    /**
     * Get reaction counts for a comment
     */
    public static function getReactionCounts($commentId)
    {
        return self::where('comment_id', $commentId)
                  ->selectRaw('reaction_type, COUNT(*) as count')
                  ->groupBy('reaction_type')
                  ->pluck('count', 'reaction_type')
                  ->toArray();
    }

    /**
     * Get user's reaction for a comment
     */
    public static function getUserReaction($commentId, $userId)
    {
        return self::where('comment_id', $commentId)
                  ->where('user_id', $userId)
                  ->first();
    }
}
