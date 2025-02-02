    <div class="notifications-list">
        {{-- @foreach ($notifications as $notification)
            <div class="notification-item dropdown-item {{ $notification->seen_at ? 'notification-seen' : 'notification-unseen' }}"
                data-seen-at="{{ $notification->seen_at }}" data-stopPropagation="true">
                <div class="d-flex align-items-center">
                    <div class="mr-2">
                        <img src="{{ $notification->icon }}" class="notification-icon" alt="notifications icon" />
                    </div>
                    <div class="w-100">
                        <a href="{{ $notification->route }}" class="notification-link text-decoration-none text-dark">
                            <span class="font-weight-bold">{{ $notification->name }}</span>
                            <p class="my-0 break-spaces text-muted f-13">{{ $notification->description }}</p>
                        </a>
                        <div class="row">
                            <div class="col-10">
                                <small
                                    class="text-muted text-right">{{ $notification->created_at?->diffForHumans() }}</small>
                            </div>
                            <div class="col-2">
                                <div class="btn-group dropdown options-dropdown">
                                    <button type="button" class="btn btn-xs btn-transparent notification-menu-btn"
                                        data-toggle="dropdown" data-boundary="window">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right notification-options-menu">
                                        <button class="dropdown-item" type="button"
                                            onclick="markAsRead({{ $notification->id }})">
                                            <i class="fas fa-check mr-2"></i> Mark as read
                                        </button>
                                        <button class="dropdown-item text-danger" type="button"
                                            onclick="deleteNotification({{ $notification->id }})">
                                            <i class="fas fa-trash-alt mr-2"></i> Delete this
                                            notification
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dropdown-divider"></div>
        @endforeach --}}
    </div>