@foreach($comments as $comment)
<div class="comment-item mb-4 p-3 bg-light rounded" data-comment-id="{{ $comment->id }}">
    <!-- Comment Header -->
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div class="d-flex align-items-center">
            @if($comment->user->avatar)
                <img src="{{ $comment->user->avatar }}" class="rounded-circle me-2" width="32" height="32">
            @else
                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                     style="width: 32px; height: 32px;">
                    <i class="fas fa-user text-white"></i>
                </div>
            @endif
            <div>
                <strong>{{ $comment->user->name }}</strong>
                <small class="text-muted d-block">{{ $comment->created_at->diffForHumans() }}</small>
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @can('update', $comment)
                    <li><a class="dropdown-item" href="#" onclick="editComment({{ $comment->id }})">
                        <i class="fas fa-edit"></i> @translate('edit')
                    </a></li>
                @endcan
                <li><a class="dropdown-item" href="#" onclick="replyToComment({{ $comment->id }})">
                    <i class="fas fa-reply"></i> @translate('reply')
                </a></li>
                @can('delete', $comment)
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteComment({{ $comment->id }})">
                        <i class="fas fa-trash"></i> @translate('delete')
                    </a></li>
                @endcan
            </ul>
        </div>
    </div>

    <!-- Comment Content -->
    <div class="comment-content mb-3">
        {!! nl2br(e($comment->comment)) !!}
    </div>

    <!-- Comment Attachments -->
    @if($comment->attachments->count() > 0)
        <div class="comment-attachments mb-3">
            <div class="row g-2">
                @foreach($comment->attachments as $attachment)
                    <div class="col-md-6">
                        <div class="card card-sm">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        @if($attachment->isImage())
                                            <i class="fas fa-image text-primary"></i>
                                        @elseif($attachment->isDocument())
                                            <i class="fas fa-file-alt text-info"></i>
                                        @else
                                            <i class="fas fa-file text-secondary"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small">
                                            <a href="{{ $attachment->attachment_url }}" target="_blank" class="text-decoration-none">
                                                {{ $attachment->original_name }}
                                            </a>
                                        </div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            {{ $attachment->formatted_size }}
                                        </div>
                                    </div>
                                    @if($attachment->isImage() && $attachment->thumbnail_url)
                                        <div class="ms-2">
                                            <img src="{{ $attachment->thumbnail_url }}" class="rounded" width="40" height="40">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Comment Reactions -->
    <div class="comment-reactions mb-3">
        <div class="d-flex align-items-center gap-2">
            @foreach(\Modules\Comment\Entities\CommentReaction::getReactionTypes() as $type => $emoji)
                @php
                    $count = $comment->reactions->where('reaction_type', $type)->count();
                    $userReacted = $comment->reactions->where('reaction_type', $type)->where('user_id', auth()->id())->count() > 0;
                @endphp
                @if($count > 0 || $type === 'like')
                    <button type="button" 
                            class="reaction-btn {{ $userReacted ? 'active' : '' }}"
                            onclick="toggleReaction({{ $comment->id }}, '{{ $type }}')">
                        {{ $emoji }} {{ $count > 0 ? $count : '' }}
                    </button>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Reply Form (Hidden by default) -->
    <div class="reply-form" id="replyForm{{ $comment->id }}" style="display: none;">
        <form onsubmit="submitReply(event, {{ $comment->id }})">
            <div class="mb-2">
                <textarea class="form-control form-control-sm" name="comment" rows="3" 
                          placeholder="@translate('write_a_reply')" required></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-secondary" onclick="cancelReply({{ $comment->id }})">
                    @translate('cancel')
                </button>
                <button type="submit" class="btn btn-sm btn-primary">
                    @translate('reply')
                </button>
            </div>
        </form>
    </div>

    <!-- Replies -->
    @if($comment->replies->count() > 0)
        <div class="replies mt-3 ps-4 border-start">
            @foreach($comment->replies as $reply)
                <div class="reply-item mb-3 p-2 bg-white rounded" data-comment-id="{{ $reply->id }}">
                    <!-- Reply Header -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center">
                            @if($reply->user->avatar)
                                <img src="{{ $reply->user->avatar }}" class="rounded-circle me-2" width="24" height="24">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 24px; height: 24px;">
                                    <i class="fas fa-user text-white" style="font-size: 10px;"></i>
                                </div>
                            @endif
                            <div>
                                <strong class="small">{{ $reply->user->name }}</strong>
                                <small class="text-muted d-block" style="font-size: 11px;">{{ $reply->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v" style="font-size: 12px;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('update', $reply)
                                    <li><a class="dropdown-item" href="#" onclick="editComment({{ $reply->id }})">
                                        <i class="fas fa-edit"></i> @translate('edit')
                                    </a></li>
                                @endcan
                                @can('delete', $reply)
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteComment({{ $reply->id }})">
                                        <i class="fas fa-trash"></i> @translate('delete')
                                    </a></li>
                                @endcan
                            </ul>
                        </div>
                    </div>

                    <!-- Reply Content -->
                    <div class="reply-content small">
                        {!! nl2br(e($reply->comment)) !!}
                    </div>

                    <!-- Reply Attachments -->
                    @if($reply->attachments->count() > 0)
                        <div class="reply-attachments mt-2">
                            @foreach($reply->attachments as $attachment)
                                <div class="d-inline-block me-2 mb-1">
                                    <a href="{{ $attachment->attachment_url }}" target="_blank" 
                                       class="btn btn-sm btn-outline-secondary">
                                        @if($attachment->isImage())
                                            <i class="fas fa-image"></i>
                                        @else
                                            <i class="fas fa-file"></i>
                                        @endif
                                        {{ Str::limit($attachment->original_name, 15) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Reply Reactions -->
                    <div class="reply-reactions mt-2">
                        <div class="d-flex align-items-center gap-1">
                            @foreach(\Modules\Comment\Entities\CommentReaction::getReactionTypes() as $type => $emoji)
                                @php
                                    $count = $reply->reactions->where('reaction_type', $type)->count();
                                    $userReacted = $reply->reactions->where('reaction_type', $type)->where('user_id', auth()->id())->count() > 0;
                                @endphp
                                @if($count > 0 || $type === 'like')
                                    <button type="button" 
                                            class="reaction-btn small {{ $userReacted ? 'active' : '' }}"
                                            onclick="toggleReaction({{ $reply->id }}, '{{ $type }}')"
                                            style="font-size: 11px; padding: 1px 6px;">
                                        {{ $emoji }} {{ $count > 0 ? $count : '' }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endforeach

@if($comments->isEmpty())
    <div class="text-center text-muted py-4">
        <i class="fas fa-comments fa-3x mb-3"></i>
        <p>@translate('no_comments_yet')</p>
        <p class="small">@translate('be_first_to_comment')</p>
    </div>
@endif

<script>
function toggleReaction(commentId, reactionType) {
    fetch(`/tenant/comments/${commentId}/reaction`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            reaction_type: reactionType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the comments section
            location.reload();
        } else {
            toastr.error(data.message || '@translate("error_updating_reaction")');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('@translate("error_updating_reaction")');
    });
}

function replyToComment(commentId) {
    const replyForm = document.getElementById(`replyForm${commentId}`);
    replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
    
    if (replyForm.style.display === 'block') {
        replyForm.querySelector('textarea').focus();
    }
}

function cancelReply(commentId) {
    const replyForm = document.getElementById(`replyForm${commentId}`);
    replyForm.style.display = 'none';
    replyForm.querySelector('form').reset();
}

function submitReply(event, parentId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    fetch(`/tenant/comments/${parentId}/reply`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success('@translate("reply_added_successfully")');
            location.reload(); // Refresh to show new reply
        } else {
            toastr.error(data.message || '@translate("error_adding_reply")');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('@translate("error_adding_reply")');
    });
}

function editComment(commentId) {
    // Implement edit functionality
    toastr.info('@translate("edit_functionality_coming_soon")');
}

function deleteComment(commentId) {
    if (confirm('@translate("confirm_delete_comment")')) {
        fetch(`/tenant/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('@translate("comment_deleted_successfully")');
                document.querySelector(`[data-comment-id="${commentId}"]`).remove();
            } else {
                toastr.error(data.message || '@translate("error_deleting_comment")');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('@translate("error_deleting_comment")');
        });
    }
}
</script>


