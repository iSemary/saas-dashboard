<?php

namespace Modules\Comment\Services;

use Modules\Comment\Entities\Comment;
use Modules\Comment\Repositories\CommentInterface;
use Illuminate\Http\UploadedFile;
use Modules\Comment\Entities\CommentAttachment;

class CommentService
{
    protected $repository;
    public $model;

    public function __construct(CommentInterface $repository, Comment $comment)
    {
        $this->model = $comment;
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function getDataTables()
    {
        return $this->repository->datatables();
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        // Handle attachments if present
        $attachments = $data['attachments'] ?? [];
        unset($data['attachments']);

        $comment = $this->repository->create($data);

        if (!empty($attachments)) {
            $this->handleAttachments($comment, $attachments);
        }

        return $comment;
    }

    public function update($id, array $data)
    {
        // Handle attachments if present
        $attachments = $data['attachments'] ?? [];
        unset($data['attachments']);

        $comment = $this->repository->update($id, $data);

        if (!empty($attachments) && $comment) {
            $this->handleAttachments($comment, $attachments);
        }

        return $comment;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function restore($id)
    {
        return $this->repository->restore($id);
    }

    /**
     * Get comments for a specific object (ticket, post, etc.)
     */
    public function getCommentsForObject($objectId, $objectModel)
    {
        return $this->repository->getCommentsForObject($objectId, $objectModel);
    }

    /**
     * Get top-level comments only
     */
    public function getTopLevelComments($objectId, $objectModel)
    {
        return $this->repository->getTopLevelComments($objectId, $objectModel);
    }

    /**
     * Get replies for a comment
     */
    public function getReplies($parentId)
    {
        return $this->repository->getReplies($parentId);
    }

    /**
     * Add a reply to a comment
     */
    public function addReply($parentId, array $data)
    {
        $parent = $this->repository->find($parentId);
        if (!$parent) {
            throw new \Exception('Parent comment not found');
        }

        $data['parent_id'] = $parentId;
        $data['object_id'] = $parent->object_id;
        $data['object_model'] = $parent->object_model;

        return $this->create($data);
    }

    /**
     * Add or toggle reaction
     */
    public function addReaction($commentId, $userId, $reactionType)
    {
        return $this->repository->addReaction($commentId, $userId, $reactionType);
    }

    /**
     * Remove reaction
     */
    public function removeReaction($commentId, $userId)
    {
        return $this->repository->removeReaction($commentId, $userId);
    }

    /**
     * Mark comment as seen
     */
    public function markAsSeen($commentId)
    {
        return $this->repository->markAsSeen($commentId);
    }

    /**
     * Get unseen comments count for user
     */
    public function getUnseenCount($userId)
    {
        return $this->repository->getUnseenCount($userId);
    }

    /**
     * Handle file attachments
     */
    protected function handleAttachments(Comment $comment, array $attachments)
    {
        foreach ($attachments as $attachment) {
            if ($attachment instanceof UploadedFile) {
                CommentAttachment::create([
                    'comment_id' => $comment->id,
                    'attachment_url' => $attachment,
                    'user_id' => $comment->user_id,
                ]);
            }
        }
    }

    /**
     * Get paginated comments
     */
    public function getPaginatedComments($objectId, $objectModel, $perPage = 10)
    {
        return $this->repository->getPaginatedComments($objectId, $objectModel, $perPage);
    }

    /**
     * Search comments
     */
    public function searchComments($query, $objectId = null, $objectModel = null)
    {
        return $this->repository->searchComments($query, $objectId, $objectModel);
    }

    /**
     * Get recent comments for user
     */
    public function getRecentComments($userId, $limit = 10)
    {
        return $this->repository->getRecentComments($userId, $limit);
    }

    /**
     * Get comment statistics
     */
    public function getCommentStats($objectId = null, $objectModel = null)
    {
        return $this->repository->getCommentStats($objectId, $objectModel);
    }

    /**
     * Bulk mark comments as seen
     */
    public function bulkMarkAsSeen(array $commentIds)
    {
        return $this->repository->bulkMarkAsSeen($commentIds);
    }

    /**
     * Get comments by date range
     */
    public function getCommentsByDateRange($startDate, $endDate, $objectId = null, $objectModel = null)
    {
        return $this->repository->getCommentsByDateRange($startDate, $endDate, $objectId, $objectModel);
    }

    /**
     * Create a comment with rich content
     */
    public function createRichComment(array $data)
    {
        // Sanitize HTML content if present
        if (isset($data['comment']) && $this->isHtml($data['comment'])) {
            $data['comment'] = $this->sanitizeHtml($data['comment']);
        }

        return $this->create($data);
    }

    /**
     * Check if content is HTML
     */
    protected function isHtml($content)
    {
        return $content !== strip_tags($content);
    }

    /**
     * Sanitize HTML content
     */
    protected function sanitizeHtml($html)
    {
        // Allow basic HTML tags for rich text
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><blockquote><code><pre><h1><h2><h3><h4><h5><h6>';
        return strip_tags($html, $allowedTags);
    }

    /**
     * Get comment thread (comment with all its nested replies)
     */
    public function getCommentThread($commentId)
    {
        $comment = $this->repository->find($commentId);
        if (!$comment) {
            return null;
        }

        // If it's a reply, get the root comment
        $rootComment = $comment->getRootComment();
        
        return $this->repository->find($rootComment->id);
    }

    /**
     * Get comment activity for dashboard
     */
    public function getCommentActivity($limit = 10)
    {
        return $this->model->with(['user', 'commentable'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'user' => $comment->user->name ?? 'Unknown',
                    'action' => $comment->parent_id ? 'replied to' : 'commented on',
                    'object' => class_basename($comment->object_model),
                    'excerpt' => $comment->excerpt,
                    'created_at' => $comment->created_at,
                ];
            });
    }
}
