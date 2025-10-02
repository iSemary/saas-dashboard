<?php

namespace Modules\Comment\Repositories;

use App\Helpers\TableHelper;
use Illuminate\Support\Facades\DB;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Entities\CommentReaction;
use Modules\Comment\Entities\CommentAttachment;
use Yajra\DataTables\DataTables;

class CommentRepository implements CommentInterface
{
    protected $model;

    public function __construct(Comment $comment)
    {
        $this->model = $comment;
    }

    public function all()
    {
        return $this->model->with(['user', 'attachments', 'reactions'])->get();
    }

    public function datatables()
    {
        $rows = $this->model->query()
            ->with(['user', 'attachments'])
            ->withCount(['reactions', 'replies'])
            ->where(function ($q) {
                if (request()->from_date && request()->to_date) {
                    TableHelper::loopOverDates(5, $q, $this->model->getTable(), [request()->from_date, request()->to_date]);
                }
            });

        return DataTables::of($rows)
            ->addColumn('user_name', function ($row) {
                return $row->user->name ?? 'Unknown';
            })
            ->addColumn('excerpt', function ($row) {
                return $row->excerpt;
            })
            ->addColumn('attachments_count', function ($row) {
                return $row->attachments->count();
            })
            ->addColumn('actions', function ($row) {
                return TableHelper::actionButtons(
                    row: $row,
                    editRoute: 'landlord.comments.edit',
                    deleteRoute: 'landlord.comments.destroy',
                    restoreRoute: 'landlord.comments.restore',
                    type: "comments",
                    titleType: "comment",
                    showIconsOnly: true
                );
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function find($id)
    {
        return $this->model->with(['user', 'attachments', 'reactions.user', 'replies.user'])->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $comment = $this->model->find($id);
        if ($comment) {
            $comment->update($data);
            return $comment;
        }
        return null;
    }

    public function delete($id)
    {
        $comment = $this->model->find($id);
        if ($comment) {
            $comment->delete();
            return true;
        }
        return false;
    }

    public function restore($id)
    {
        $comment = $this->model->withTrashed()->find($id);
        if ($comment) {
            $comment->restore();
            return true;
        }
        return false;
    }

    public function getCommentsForObject($objectId, $objectModel)
    {
        return $this->model->forObject($objectId, $objectModel)
            ->with(['user', 'attachments', 'reactions.user', 'allReplies.user', 'allReplies.attachments', 'allReplies.reactions.user'])
            ->topLevel()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getTopLevelComments($objectId, $objectModel)
    {
        return $this->model->forObject($objectId, $objectModel)
            ->with(['user', 'attachments', 'reactions.user'])
            ->withCount('replies')
            ->topLevel()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getReplies($parentId)
    {
        return $this->model->where('parent_id', $parentId)
            ->with(['user', 'attachments', 'reactions.user', 'allReplies.user'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function addReaction($commentId, $userId, $reactionType)
    {
        return CommentReaction::toggleReaction($commentId, $userId, $reactionType);
    }

    public function removeReaction($commentId, $userId)
    {
        return CommentReaction::where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->delete();
    }

    public function markAsSeen($commentId)
    {
        $comment = $this->model->find($commentId);
        if ($comment) {
            $comment->markAsSeen();
            return true;
        }
        return false;
    }

    public function getUnseenCount($userId)
    {
        return $this->model->unseen()
            ->where('user_id', '!=', $userId)
            ->count();
    }

    /**
     * Get comments with pagination for an object
     */
    public function getPaginatedComments($objectId, $objectModel, $perPage = 10)
    {
        return $this->model->forObject($objectId, $objectModel)
            ->with(['user', 'attachments', 'reactions.user'])
            ->withCount('replies')
            ->topLevel()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search comments by content
     */
    public function searchComments($query, $objectId = null, $objectModel = null)
    {
        $builder = $this->model->where('comment', 'like', "%{$query}%")
            ->with(['user', 'attachments']);

        if ($objectId && $objectModel) {
            $builder->forObject($objectId, $objectModel);
        }

        return $builder->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get recent comments for a user
     */
    public function getRecentComments($userId, $limit = 10)
    {
        return $this->model->where('user_id', $userId)
            ->with(['attachments'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get comment statistics
     */
    public function getCommentStats($objectId = null, $objectModel = null)
    {
        $query = $this->model->query();

        if ($objectId && $objectModel) {
            $query->forObject($objectId, $objectModel);
        }

        return [
            'total_comments' => $query->count(),
            'total_replies' => $query->whereNotNull('parent_id')->count(),
            'total_attachments' => CommentAttachment::whereIn('comment_id', $query->pluck('id'))->count(),
            'total_reactions' => CommentReaction::whereIn('comment_id', $query->pluck('id'))->count(),
            'unseen_comments' => $query->unseen()->count(),
        ];
    }

    /**
     * Bulk mark comments as seen
     */
    public function bulkMarkAsSeen(array $commentIds)
    {
        return $this->model->whereIn('id', $commentIds)
            ->update(['seen' => true]);
    }

    /**
     * Get comments by date range
     */
    public function getCommentsByDateRange($startDate, $endDate, $objectId = null, $objectModel = null)
    {
        $query = $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->with(['user', 'attachments']);

        if ($objectId && $objectModel) {
            $query->forObject($objectId, $objectModel);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
