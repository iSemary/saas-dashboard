@extends('layouts.tenant.app')

@section('content')
<div class="container-fluid" x-data="ticketDetail({{ $ticketData['ticket']->id }})">
    <div class="row">
        <!-- Main Ticket Content -->
        <div class="col-lg-8">
            <!-- Ticket Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                <span class="text-muted">{{ $ticketData['ticket']->ticket_number }}</span>
                                - {{ $ticketData['ticket']->title }}
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('tenant.tickets.edit', $ticketData['ticket']->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> @translate('edit')
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-info" @click="printTicket()">
                                    <i class="fas fa-print"></i> @translate('print')
                                </button>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                            data-bs-toggle="dropdown">
                                        @translate('actions')
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" @click="assignTicket()">
                                            <i class="fas fa-user-plus"></i> @translate('assign')
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" @click="changeStatus()">
                                            <i class="fas fa-tasks"></i> @translate('change_status')
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" @click="changePriority()">
                                            <i class="fas fa-flag"></i> @translate('change_priority')
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        @if($ticketData['ticket']->status !== 'closed')
                                            <li><a class="dropdown-item text-warning" href="#" @click="closeTicket()">
                                                <i class="fas fa-times-circle"></i> @translate('close_ticket')
                                            </a></li>
                                        @else
                                            <li><a class="dropdown-item text-success" href="#" @click="reopenTicket()">
                                                <i class="fas fa-redo"></i> @translate('reopen_ticket')
                                            </a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Ticket Metadata -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>@translate('status'):</strong><br>
                            <span class="badge {{ $ticketData['ticket']->getStatusBadgeClass() }}">
                                {{ ucfirst(str_replace('_', ' ', $ticketData['ticket']->status)) }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>@translate('priority'):</strong><br>
                            <span class="badge {{ $ticketData['ticket']->getPriorityBadgeClass() }}">
                                {{ ucfirst($ticketData['ticket']->priority) }}
                            </span>
                            @if($ticketData['ticket']->isOverdue())
                                <span class="badge badge-danger ms-1">
                                    <i class="fas fa-exclamation-triangle"></i> @translate('overdue')
                                </span>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <strong>@translate('assignee'):</strong><br>
                            @if($ticketData['ticket']->assignee)
                                <div class="d-flex align-items-center">
                                    @if($ticketData['ticket']->assignee->avatar)
                                        <img src="{{ $ticketData['ticket']->assignee->avatar }}" 
                                             class="rounded-circle me-2" width="24" height="24">
                                    @endif
                                    {{ $ticketData['ticket']->assignee->name }}
                                </div>
                            @else
                                <span class="text-muted">@translate('unassigned')</span>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <strong>@translate('created_by'):</strong><br>
                            <div class="d-flex align-items-center">
                                @if($ticketData['ticket']->creator->avatar)
                                    <img src="{{ $ticketData['ticket']->creator->avatar }}" 
                                         class="rounded-circle me-2" width="24" height="24">
                                @endif
                                {{ $ticketData['ticket']->creator->name }}
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Dates -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>@translate('created_at'):</strong><br>
                            <small class="text-muted">{{ $ticketData['ticket']->created_at->format('M d, Y H:i') }}</small>
                        </div>
                        @if($ticketData['ticket']->due_date)
                            <div class="col-md-3">
                                <strong>@translate('due_date'):</strong><br>
                                <small class="text-muted">{{ $ticketData['ticket']->due_date->format('M d, Y H:i') }}</small>
                            </div>
                        @endif
                        @if($ticketData['ticket']->resolved_at)
                            <div class="col-md-3">
                                <strong>@translate('resolved_at'):</strong><br>
                                <small class="text-muted">{{ $ticketData['ticket']->resolved_at->format('M d, Y H:i') }}</small>
                            </div>
                        @endif
                        @if($ticketData['ticket']->closed_at)
                            <div class="col-md-3">
                                <strong>@translate('closed_at'):</strong><br>
                                <small class="text-muted">{{ $ticketData['ticket']->closed_at->format('M d, Y H:i') }}</small>
                            </div>
                        @endif
                    </div>

                    <!-- Tags -->
                    @if($ticketData['ticket']->tags && count($ticketData['ticket']->tags) > 0)
                        <div class="mb-4">
                            <strong>@translate('tags'):</strong><br>
                            @foreach($ticketData['ticket']->tags as $tag)
                                <span class="badge bg-light text-dark me-1">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Ticket Description -->
                    <div class="mb-4">
                        <strong>@translate('description'):</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            @if($ticketData['ticket']->html_content)
                                {!! $ticketData['ticket']->html_content !!}
                            @else
                                {!! nl2br(e($ticketData['ticket']->description)) !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            @translate('comments') 
                            <span class="badge bg-secondary">{{ $ticketData['comment_stats']['total_comments'] ?? 0 }}</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary" @click="showAddCommentForm = !showAddCommentForm">
                            <i class="fas fa-plus"></i> @translate('add_comment')
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Add Comment Form -->
                    <div x-show="showAddCommentForm" x-transition class="mb-4">
                        <form @submit.prevent="addComment()">
                            <div class="mb-3">
                                <textarea class="form-control" x-model="newComment.comment" rows="4" 
                                          placeholder="@translate('enter_your_comment')" required></textarea>
                            </div>
                            <div class="mb-3">
                                <input type="file" class="form-control" x-ref="commentAttachments" multiple
                                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                                <div class="form-text">@translate('max_file_size_10mb')</div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" @click="showAddCommentForm = false">
                                    @translate('cancel')
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="!newComment.comment.trim()">
                                    <i class="fas fa-comment"></i> @translate('add_comment')
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div id="commentsList">
                        @include('tenant.ticket.tickets.partials.comments', ['comments' => $ticketData['comments']])
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- SLA Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">@translate('sla_information')</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>@translate('time_in_current_status'):</strong><br>
                        <span class="text-primary">{{ $ticketData['ticket']->formatDuration($ticketData['ticket']->getTimeInCurrentStatus()) }}</span>
                    </div>
                    @if($ticketData['ticket']->sla_data)
                        @foreach($ticketData['ticket']->sla_data['status_history'] ?? [] as $history)
                            @if($history['duration'] > 0)
                                <div class="small mb-2">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $history['status'])) }}:</strong>
                                    {{ $ticketData['ticket']->formatDuration($history['duration']) }}
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">@translate('activity_timeline')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="timeline">
                        @foreach($timeline as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $activity['type'] === 'status_change' ? 'primary' : 'info' }}"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $activity['title'] }}</strong>
                                            @if($activity['description'])
                                                <p class="mb-1 text-muted small">{{ $activity['description'] }}</p>
                                            @endif
                                            <small class="text-muted">
                                                {{ $activity['user'] }} • {{ $activity['timestamp']->diffForHumans() }}
                                            </small>
                                        </div>
                                        @if($activity['user_avatar'])
                                            <img src="{{ $activity['user_avatar'] }}" class="rounded-circle" width="32" height="32">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">@translate('quick_actions')</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" @click="assignTicket()">
                            <i class="fas fa-user-plus"></i> @translate('assign_ticket')
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" @click="changeStatus()">
                            <i class="fas fa-tasks"></i> @translate('change_status')
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" @click="changePriority()">
                            <i class="fas fa-flag"></i> @translate('change_priority')
                        </button>
                        @if($ticketData['ticket']->status !== 'closed')
                            <button type="button" class="btn btn-outline-danger btn-sm" @click="closeTicket()">
                                <i class="fas fa-times-circle"></i> @translate('close_ticket')
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-success btn-sm" @click="reopenTicket()">
                                <i class="fas fa-redo"></i> @translate('reopen_ticket')
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Modals -->
@include('tenant.ticket.tickets.modals.assign')
@include('tenant.ticket.tickets.modals.status')
@include('tenant.ticket.tickets.modals.priority')
@endsection

@section('scripts')
<script src="{{ asset('assets/tenant/js/ticket/tickets/show.js') }}"></script>
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #0d6efd;
}

.comment-item {
    border-left: 3px solid #e9ecef;
    transition: border-color 0.2s ease;
}

.comment-item:hover {
    border-left-color: #0d6efd;
}

.comment-reactions {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.reaction-btn {
    border: 1px solid #dee2e6;
    background: #fff;
    border-radius: 20px;
    padding: 2px 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.reaction-btn:hover {
    background: #f8f9fa;
    border-color: #0d6efd;
}

.reaction-btn.active {
    background: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

@media print {
    .btn, .card-header .col-md-6:last-child, .timeline, .card:last-child {
        display: none !important;
    }
}
</style>
@endsection


