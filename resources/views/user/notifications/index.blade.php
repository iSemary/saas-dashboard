@extends('layouts.' . $layoutPrefix . '.app')
@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                @translate('notifications')
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" id="page-mark-all-read">
                    <i class="fas fa-check-double"></i> @translate('mark_all_as_read')
                </button>
                <button type="button" class="btn btn-tool" id="page-delete-all">
                    <i class="fas fa-trash"></i> @translate('delete_all')
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="page-notifications-container">
                <div class="page-notifications-list">
                    <!-- Notifications will be loaded here -->
                </div>
                <div class="text-center mt-3" id="page-load-more-container" style="display: none;">
                    <button class="btn btn-primary" id="page-load-more-btn">
                        @translate('load_more')
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/shared/js/notifications/index.js') }}"></script>
@endsection
