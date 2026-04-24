let pageCurrentPage = 1;
let pageLastPage = 1;

document.addEventListener("DOMContentLoaded", function() {
    loadPageNotifications(1);
    setupPageEventListeners();
});

function setupPageEventListeners() {
    // Mark all as read
    document.getElementById('page-mark-all-read').addEventListener('click', function() {
        if (confirm(translate('confirm_mark_all_read'))) {
            markAllNotificationsAsRead();
        }
    });

    // Delete all notifications
    document.getElementById('page-delete-all').addEventListener('click', function() {
        if (confirm(translate('confirm_delete_all'))) {
            deleteAllPageNotifications();
        }
    });

    // Load more button
    document.getElementById('page-load-more-btn').addEventListener('click', function() {
        loadPageNotifications(pageCurrentPage + 1);
    });
}

function loadPageNotifications(page) {
    const notificationsContainer = document.querySelector('.page-notifications-list');
    
    $.ajax({
        url: route('notifications.list'),
        type: 'GET',
        data: { page: page },
        beforeSend: function() {
            if (page === 1) {
                notificationsContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
            } else {
                document.getElementById('page-load-more-btn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            }
        },
        success: function(response) {
            renderPageNotifications(response, page === 1);
        },
        error: function(xhr) {
            console.error('Error loading notifications:', xhr);
            notificationsContainer.innerHTML = '<div class="alert alert-danger">Error loading notifications</div>';
        }
    });
}

function renderPageNotifications(response, clearExisting) {
    const notificationsContainer = document.querySelector('.page-notifications-list');
    const loadMoreContainer = document.getElementById('page-load-more-container');
    const notifications = response.data.data.data;
    pageCurrentPage = response.data.data.current_page;
    pageLastPage = response.data.data.last_page;

    if (clearExisting) {
        notificationsContainer.innerHTML = '';
    }

    if (notifications.length === 0 && pageCurrentPage === 1) {
        notificationsContainer.innerHTML = `
            <div class="text-center text-muted p-4">
                ${translate('no_notifications_yet')}
            </div>
        `;
        loadMoreContainer.style.display = 'none';
        return;
    }

    notifications.forEach(notification => {
        const notificationElement = createPageNotificationElement(notification);
        notificationsContainer.appendChild(notificationElement);
    });

    // Show/hide load more button
    loadMoreContainer.style.display = pageCurrentPage < pageLastPage ? 'block' : 'none';
    document.getElementById('page-load-more-btn').innerHTML = translate('load_more');
}

function createPageNotificationElement(notification) {
    const div = document.createElement('div');
    div.className = `page-notification-item p-3 border-bottom ${notification.seen_at ? 'bg-light' : ''}`;
    div.innerHTML = `
        <div class="d-flex">
            <div class="mr-3">
                <img src="${notification.icon}" class="page-notification-icon" alt="notification icon" style="width: 40px; height: 40px;">
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="mb-1 ${notification.seen_at ? '' : 'font-weight-bold'}">${notification.name}</h5>
                    <div class="page-notification-dropdown dropdown">
                        <button class="btn btn-sm btn-link text-muted" type="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <button class="dropdown-item" onclick="markPageNotificationAsRead(${notification.id})">
                                <i class="fas fa-check mr-2"></i> ${translate('mark_as_read')}
                            </button>
                            <button class="dropdown-item text-danger" onclick="deletePageNotification(${notification.id})">
                                <i class="fas fa-trash mr-2"></i> ${translate('delete')}
                            </button>
                        </div>
                    </div>
                </div>
                <p class="mb-1 text-muted">${notification.description}</p>
                <small class="text-muted" title="${notification.created_at}">${notification.created_at_diff}</small>
            </div>
        </div>
    `;
    return div;
}

function markPageNotificationAsRead(notificationId) {
    $.ajax({
        url: route('notifications.mark-as-read', { notification: notificationId }),
        type: 'POST',
        success: function() {
            loadPageNotifications(1);
        },
        error: function(xhr) {
            console.error('Error marking notification as read:', xhr);
        }
    });
}

function markPageNotificationAsUnRead(notificationId) {
    $.ajax({
        url: route('notifications.mark-as-unread', { notification: notificationId }),
        type: 'POST',
        success: function() {
            loadPageNotifications(1);
        },
        error: function(xhr) {
            console.error('Error marking notification as unread:', xhr);
        }
    });
}

function deletePageNotification(notificationId) {
    if (!confirm(translate('confirm_delete_notification'))) {
        return;
    }

    $.ajax({
        url: route('notifications.destroy', { notification: notificationId }),
        type: 'DELETE',
        success: function() {
            loadPageNotifications(1);
        },
        error: function(xhr) {
            console.error('Error deleting notification:', xhr);
        }
    });
}

function markAllNotificationsAsRead() {
    $.ajax({
        url: route('notifications.mark-all-as-read'),
        type: 'POST',
        success: function() {
            loadPageNotifications(1);
        },
        error: function(xhr) {
            console.error('Error marking all notifications as read:', xhr);
        }
    });
}

function deleteAllPageNotifications() {
    $.ajax({
        url: route('notifications.destroyAll'),
        type: 'DELETE',
        success: function() {
            loadPageNotifications(1);
        },
        error: function(xhr) {
            console.error('Error deleting all notifications:', xhr);
        }
    });
}