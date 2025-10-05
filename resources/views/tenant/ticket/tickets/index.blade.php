@extends('layouts.tenant.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    @include('layouts.shared.filter-date')
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="statusFilter">
                                <option value="">@translate('all_statuses')</option>
                                @foreach($statusOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="priorityFilter">
                                <option value="">@translate('all_priorities')</option>
                                @foreach($priorityOptions as $key => $label)
                                    <option value="{{ $key }}">{{ ucfirst($label) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select form-select-sm" id="assigneeFilter">
                                <option value="">@translate('all_assignees')</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="table" data-route="{{ route('tenant.tickets.index') }}"
                    class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th scope="col">#</th>
                            <th scope="col">@translate('ticket_number')</th>
                            <th scope="col">@translate('title')</th>
                            <th scope="col">@translate('status')</th>
                            <th scope="col">@translate('priority')</th>
                            <th scope="col">@translate('creator')</th>
                            <th scope="col">@translate('assignee')</th>
                            <th scope="col">@translate('brand')</th>
                            <th scope="col">@translate('comments')</th>
                            <th scope="col">@translate('created_at')</th>
                            <th scope="col">@translate('action')</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div class="card mt-3" id="bulkActionsPanel" style="display: none;">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span id="selectedCount">0</span> @translate('tickets_selected')
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAssign()">
                            <i class="fas fa-user-plus"></i> @translate('bulk_assign')
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkUpdateStatus()">
                            <i class="fas fa-tasks"></i> @translate('bulk_status')
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="bulkUpdatePriority()">
                            <i class="fas fa-flag"></i> @translate('bulk_priority')
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash"></i> @translate('bulk_delete')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Modals -->
    @include('tenant.ticket.tickets.modals.bulk-assign')
    @include('tenant.ticket.tickets.modals.bulk-status')
    @include('tenant.ticket.tickets.modals.bulk-priority')
@endsection

@section('scripts')
    <script src="{{ asset('assets/tenant/js/ticket/tickets/index.js') }}"></script>
@endsection

@section('styles')
<style>
.table th {
    white-space: nowrap;
}

.ticket-priority-urgent {
    border-left: 4px solid #dc3545;
}

.ticket-priority-high {
    border-left: 4px solid #fd7e14;
}

.ticket-priority-medium {
    border-left: 4px solid #0dcaf0;
}

.ticket-priority-low {
    border-left: 4px solid #198754;
}

.overdue-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.ticket-row:hover {
    background-color: #f8f9fa;
}

.bulk-actions-panel {
    position: sticky;
    bottom: 20px;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}
</style>
@endsection


