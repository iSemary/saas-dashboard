@extends('layouts.' . $layoutPrefix . '.app')

@section('styles')
<style>
.notification-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.notification-card.unread {
    background-color: #f8f9ff;
    border-left-color: #007bff;
}

.notification-card.read {
    background-color: #ffffff;
    border-left-color: #e9ecef;
}

.notification-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.notification-type-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.notification-priority-high {
    border-left-color: #dc3545 !important;
}

.notification-priority-medium {
    border-left-color: #ffc107 !important;
}

.notification-priority-low {
    border-left-color: #28a745 !important;
}

.tab-content {
    min-height: 400px;
}

.notification-filters {
    background: #f8f9fa;
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.infinite-scroll-loading {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.notification-attachment {
    display: inline-block;
    margin: 0.25rem 0.25rem 0 0;
    padding: 0.25rem 0.5rem;
    background: #e9ecef;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    text-decoration: none;
    color: #495057;
    transition: background-color 0.2s;
}

.notification-attachment:hover {
    background: #dee2e6;
    color: #495057;
    text-decoration: none;
}

.push-notification-toggle {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 1000;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">@translate('notifications')</h1>
                    <p class="text-muted mb-0">@translate('manage_your_notifications')</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="push-notification-setup">
                        <i class="fas fa-bell"></i> @translate('enable_push_notifications')
                    </button>
                    <button type="button" class="btn btn-success" id="page-mark-all-read">
                        <i class="fas fa-check-double"></i> @translate('mark_all_as_read')
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="total-notifications">0</h4>
                            <p class="mb-0">@translate('total')</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="unread-notifications">0</h4>
                            <p class="mb-0">@translate('unread')</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="read-notifications">0</h4>
                            <p class="mb-0">@translate('read')</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope-open fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0" id="push-status">@translate('disabled')</h4>
                            <p class="mb-0">@translate('push_notifications')</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-mobile-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Tabs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="notification-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-notifications" 
                            type="button" role="tab" data-filter="all">
                        <i class="fas fa-list"></i> @translate('all')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="unread-tab" data-bs-toggle="tab" data-bs-target="#unread-notifications" 
                            type="button" role="tab" data-filter="unread">
                        <i class="fas fa-envelope"></i> @translate('unread')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="read-tab" data-bs-toggle="tab" data-bs-target="#read-notifications" 
                            type="button" role="tab" data-filter="read">
                        <i class="fas fa-envelope-open"></i> @translate('read')
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <!-- Filters -->
            <div class="notification-filters">
                <div class="row">
                    <div class="col-md-4">
                        <label for="type-filter" class="form-label">@translate('type')</label>
                        <select class="form-select" id="type-filter">
                            <option value="">@translate('all_types')</option>
                            <option value="info">@translate('info')</option>
                            <option value="alert">@translate('alert')</option>
                            <option value="announcement">@translate('announcement')</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="priority-filter" class="form-label">@translate('priority')</label>
                        <select class="form-select" id="priority-filter">
                            <option value="">@translate('all_priorities')</option>
                            <option value="high">@translate('high')</option>
                            <option value="medium">@translate('medium')</option>
                            <option value="low">@translate('low')</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search-filter" class="form-label">@translate('search')</label>
                        <input type="text" class="form-control" id="search-filter" placeholder="@translate('search_notifications')">
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="notification-tab-content">
                <div class="tab-pane fade show active" id="all-notifications" role="tabpanel">
                    <div class="page-notifications-list" data-filter="all">
                        <div class="text-center p-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                            <p class="mt-2 text-muted">@translate('loading_notifications')</p>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="unread-notifications" role="tabpanel">
                    <div class="page-notifications-list" data-filter="unread">
                        <div class="text-center p-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                            <p class="mt-2 text-muted">@translate('loading_notifications')</p>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="read-notifications" role="tabpanel">
                    <div class="page-notifications-list" data-filter="read">
                        <div class="text-center p-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                            <p class="mt-2 text-muted">@translate('loading_notifications')</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Infinite Scroll Loading -->
            <div class="infinite-scroll-loading" id="infinite-scroll-loading" style="display: none;">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">@translate('loading_more_notifications')</p>
            </div>
        </div>
    </div>
</div>

<!-- Push Notification Toggle Button -->
<div class="push-notification-toggle">
    <button class="btn btn-primary btn-lg rounded-circle" id="push-toggle-btn" style="display: none;">
        <i class="fas fa-bell" id="push-toggle-icon"></i>
    </button>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/shared/js/notifications/enhanced-index.js') }}"></script>
@endsection
