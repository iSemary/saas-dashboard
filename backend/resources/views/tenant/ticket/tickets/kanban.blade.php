@extends('layouts.tenant.app')

@section('content')
<div class="container-fluid" x-data="kanbanBoard()">
    <!-- Filters and Actions -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm" @click="refreshBoard()">
                    <i class="fas fa-sync-alt"></i> @translate('refresh')
                </button>
                <button type="button" class="btn btn-outline-info btn-sm" @click="toggleFilters()">
                    <i class="fas fa-filter"></i> @translate('filters')
                </button>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="toggleAutoRefresh()">
                    <i class="fas" :class="autoRefresh ? 'fa-pause' : 'fa-play'"></i>
                    <span x-text="autoRefresh ? '@translate('pause_auto_refresh')' : '@translate('auto_refresh')'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="card mb-3" x-show="showFilters" x-transition>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">@translate('assigned_to')</label>
                    <select class="form-select form-select-sm" x-model="filters.assigned_to" @change="applyFilters()">
                        <option value="">@translate('all_users')</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">@translate('priority')</label>
                    <select class="form-select form-select-sm" x-model="filters.priority" @change="applyFilters()">
                        <option value="">@translate('all_priorities')</option>
                        <option value="urgent">@translate('urgent')</option>
                        <option value="high">@translate('high')</option>
                        <option value="medium">@translate('medium')</option>
                        <option value="low">@translate('low')</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">@translate('search')</label>
                    <input type="text" class="form-control form-control-sm" x-model="filters.search" 
                           @input.debounce.500ms="applyFilters()" placeholder="@translate('search_tickets')">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" @click="clearFilters()">
                            <i class="fas fa-times"></i> @translate('clear_filters')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="row kanban-board" style="min-height: 70vh;">
        <template x-for="(column, status) in kanbanData" :key="status">
            <div class="col-md kanban-column">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <span x-text="column.label"></span>
                            <span class="badge bg-secondary ms-2" x-text="column.count"></span>
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                    :id="'dropdown-' + status" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu" :aria-labelledby="'dropdown-' + status">
                                <li><a class="dropdown-item" href="#" @click="bulkAction(status, 'assign')">
                                    <i class="fas fa-user-plus"></i> @translate('bulk_assign')
                                </a></li>
                                <li><a class="dropdown-item" href="#" @click="bulkAction(status, 'priority')">
                                    <i class="fas fa-flag"></i> @translate('bulk_priority')
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-2 kanban-tickets" 
                         :data-status="status"
                         @drop="handleDrop($event, status)"
                         @dragover.prevent
                         @dragenter.prevent>
                        <template x-for="ticket in column.tickets" :key="ticket.id">
                            <div class="ticket-card mb-2" 
                                 :draggable="!ticket.is_dragging"
                                 @dragstart="handleDragStart($event, ticket)"
                                 @dragend="handleDragEnd($event, ticket)">
                                <div class="card card-sm">
                                    <div class="card-body p-2">
                                        <!-- Ticket Header -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <small class="text-muted" x-text="ticket.ticket_number"></small>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-link p-0 text-muted" type="button" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" :href="'/tenant/tickets/' + ticket.id">
                                                        <i class="fas fa-eye"></i> @translate('view')
                                                    </a></li>
                                                    <li><a class="dropdown-item" :href="'/tenant/tickets/' + ticket.id + '/edit'">
                                                        <i class="fas fa-edit"></i> @translate('edit')
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#" @click="assignTicket(ticket.id)">
                                                        <i class="fas fa-user-plus"></i> @translate('assign')
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" @click="addComment(ticket.id)">
                                                        <i class="fas fa-comment"></i> @translate('add_comment')
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Ticket Title -->
                                        <h6 class="card-title mb-2" x-text="ticket.title"></h6>

                                        <!-- Priority Badge -->
                                        <div class="mb-2">
                                            <span class="badge" 
                                                  :class="'badge-' + ticket.priority_badge_class" 
                                                  x-text="ticket.priority.charAt(0).toUpperCase() + ticket.priority.slice(1)"></span>
                                            <span x-show="ticket.is_overdue" class="badge badge-danger ms-1">
                                                <i class="fas fa-exclamation-triangle"></i> @translate('overdue')
                                            </span>
                                        </div>

                                        <!-- Ticket Info -->
                                        <div class="small text-muted mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span>@translate('created'): <span x-text="ticket.created_at"></span></span>
                                                <span x-show="ticket.due_date">@translate('due'): <span x-text="ticket.due_date"></span></span>
                                            </div>
                                            <div x-show="ticket.assignee !== 'Unassigned'">
                                                <i class="fas fa-user"></i> <span x-text="ticket.assignee"></span>
                                            </div>
                                        </div>

                                        <!-- Tags -->
                                        <div class="mb-2" x-show="ticket.tags && ticket.tags.length > 0">
                                            <template x-for="tag in ticket.tags" :key="tag">
                                                <span class="badge bg-light text-dark me-1" x-text="tag"></span>
                                            </template>
                                        </div>

                                        <!-- Footer -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> <span x-text="ticket.time_in_status"></span>
                                            </small>
                                            <div>
                                                <span x-show="ticket.comments_count > 0" class="badge bg-info">
                                                    <i class="fas fa-comments"></i> <span x-text="ticket.comments_count"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Empty State -->
                        <div x-show="column.tickets.length === 0" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>@translate('no_tickets_in_status')</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
         style="background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">@translate('loading')</span>
        </div>
    </div>
</div>

<!-- Modals -->
@include('tenant.ticket.tickets.modals.assign')
@include('tenant.ticket.tickets.modals.comment')
@include('tenant.ticket.tickets.modals.bulk-action')
@endsection

@section('scripts')
<script src="{{ asset('assets/tenant/js/ticket/tickets/kanban.js') }}"></script>
@endsection

@section('styles')
<style>
.kanban-board {
    overflow-x: auto;
}

.kanban-column {
    min-width: 300px;
    max-width: 350px;
}

.kanban-tickets {
    max-height: 60vh;
    overflow-y: auto;
}

.ticket-card {
    cursor: move;
    transition: all 0.2s ease;
}

.ticket-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.ticket-card.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.kanban-tickets.drag-over {
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
}

.card-sm .card-body {
    padding: 0.75rem;
}

.badge-urgent { background-color: #dc3545 !important; }
.badge-high { background-color: #fd7e14 !important; }
.badge-medium { background-color: #0dcaf0 !important; }
.badge-low { background-color: #198754 !important; }
</style>
@endsection


