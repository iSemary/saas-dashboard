<?php

namespace Modules\Comment\Http\Controllers;

use App\Helpers\EnumHelper;
use App\Http\Controllers\ApiController;
use Modules\Comment\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Modules\Comment\Entities\CommentReaction;

class CommentController extends ApiController implements HasMiddleware
{
    protected $service;

    public function __construct(CommentService $service)
    {
        $this->service = $service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.comments', only: ['index', 'show']),
            new Middleware('permission:create.comments', only: ['create', 'store']),
            new Middleware('permission:update.comments', only: ['edit', 'update']),
            new Middleware('permission:delete.comments', only: ['destroy']),
            new Middleware('permission:restore.comments', only: ['restore']),
        ];
    }

    public function index()
    {
        
        $title = translate($this->service->model->pluralTitle);
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate($this->service->model->pluralTitle)],
        ];

        $actionButtons = [
            [
                'text' => translate("create") . " " . translate($this->service->model->singleTitle),
                'class' => 'open-create-modal btn-sm btn-success',
                'attr' => [
                    'data-modal-link' => route('landlord.comments.create'),
                    'data-modal-title' => translate("create") . " " . translate($this->service->model->singleTitle),
                ]
            ],
        ];

        return view('landlord.comment.comments.index', compact('breadcrumbs', 'title', 'actionButtons'));
    }

    public function create()
    {
        return view('landlord.comment.comments.editor');
    }

    public function store(Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
            'object_id' => 'required|integer',
            'object_model' => 'required|string',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        $comment = $this->service->create($data);
        
        return $this->return(200, translate("created_successfully"), $comment);
    }

    public function show($id)
    {
        $comment = $this->service->get($id);
        if (!$comment) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("success"), $comment);
    }

    public function edit($id)
    {
        $row = $this->service->get($id);
        return view('landlord.comment.comments.editor', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $data = $request->all();
        $comment = $this->service->update($id, $data);
        
        if (!$comment) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("updated_successfully"), $comment);
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);
        
        if (!$result) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("deleted_successfully"));
    }

    public function restore($id)
    {
        $result = $this->service->restore($id);
        
        if (!$result) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("restored_successfully"));
    }

    /**
     * Get comments for a specific object
     */
    public function getObjectComments(Request $request)
    {
        $request->validate([
            'object_id' => 'required|integer',
            'object_model' => 'required|string',
            'page' => 'nullable|integer|min:1',
        ]);

        $comments = $this->service->getCommentsForObject(
            $request->object_id,
            $request->object_model
        );

        return $this->return(200, translate("success"), $comments);
    }

    /**
     * Add a reply to a comment
     */
    public function addReply(Request $request, $parentId)
    {
        $request->validate([
            'comment' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        try {
            $reply = $this->service->addReply($parentId, $data);
            return $this->return(200, translate("created_successfully"), $reply);
        } catch (\Exception $e) {
            return $this->return(400, $e->getMessage());
        }
    }

    /**
     * Add or toggle reaction to a comment
     */
    public function addReaction(Request $request, $commentId)
    {
        $request->validate([
            'reaction_type' => 'required|string|in:' . implode(',', array_keys(CommentReaction::REACTION_TYPES)),
        ]);

        $reaction = $this->service->addReaction(
            $commentId,
            auth()->id(),
            $request->reaction_type
        );

        $message = $reaction ? translate("reaction_added") : translate("reaction_removed");
        
        return $this->return(200, $message, [
            'reaction' => $reaction,
            'counts' => CommentReaction::getReactionCounts($commentId)
        ]);
    }

    /**
     * Remove reaction from a comment
     */
    public function removeReaction($commentId)
    {
        $result = $this->service->removeReaction($commentId, auth()->id());
        
        return $this->return(200, translate("reaction_removed"), [
            'counts' => CommentReaction::getReactionCounts($commentId)
        ]);
    }

    /**
     * Mark comment as seen
     */
    public function markAsSeen($commentId)
    {
        $result = $this->service->markAsSeen($commentId);
        
        if (!$result) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("marked_as_seen"));
    }

    /**
     * Get unseen comments count
     */
    public function getUnseenCount()
    {
        $count = $this->service->getUnseenCount(auth()->id());
        
        return $this->return(200, translate("success"), ['count' => $count]);
    }

    /**
     * Search comments
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'object_id' => 'nullable|integer',
            'object_model' => 'nullable|string',
        ]);

        $comments = $this->service->searchComments(
            $request->query,
            $request->object_id,
            $request->object_model
        );

        return $this->return(200, translate("success"), $comments);
    }

    /**
     * Get comment statistics
     */
    public function getStats(Request $request)
    {
        $stats = $this->service->getCommentStats(
            $request->object_id,
            $request->object_model
        );

        return $this->return(200, translate("success"), $stats);
    }

    /**
     * Bulk mark comments as seen
     */
    public function bulkMarkAsSeen(Request $request)
    {
        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'integer|exists:comments,id',
        ]);

        $result = $this->service->bulkMarkAsSeen($request->comment_ids);
        
        return $this->return(200, translate("marked_as_seen"), ['updated' => $result]);
    }

    /**
     * Get recent comments for user
     */
    public function getRecentComments(Request $request)
    {
        $limit = $request->get('limit', 10);
        $comments = $this->service->getRecentComments(auth()->id(), $limit);
        
        return $this->return(200, translate("success"), $comments);
    }

    /**
     * Get comment activity for dashboard
     */
    public function getActivity(Request $request)
    {
        $limit = $request->get('limit', 10);
        $activity = $this->service->getCommentActivity($limit);
        
        return $this->return(200, translate("success"), $activity);
    }

    /**
     * Get comment thread (comment with all nested replies)
     */
    public function getThread($commentId)
    {
        $thread = $this->service->getCommentThread($commentId);
        
        if (!$thread) {
            return $this->return(404, translate("not_found"));
        }

        return $this->return(200, translate("success"), $thread);
    }
}
