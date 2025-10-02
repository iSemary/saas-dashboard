/**
 * Enhanced Notifications JavaScript
 * Supports WebSocket real-time updates, push notifications, infinite scroll, and filtering
 */

class NotificationManager {
    constructor() {
        this.currentPage = 1;
        this.lastPage = 1;
        this.currentFilter = 'all';
        this.isLoading = false;
        this.notifications = new Map();
        this.pushSubscription = null;
        this.serviceWorker = null;
        
        this.init();
    }

    async init() {
        this.setupEventListeners();
        this.setupInfiniteScroll();
        this.setupWebSocketListeners();
        await this.setupPushNotifications();
        this.loadNotifications();
        this.loadStats();
    }

    setupEventListeners() {
        // Tab switching
        document.querySelectorAll('#notification-tabs button[data-filter]').forEach(tab => {
            tab.addEventListener('click', (e) => {
                this.currentFilter = e.target.dataset.filter;
                this.currentPage = 1;
                this.notifications.clear();
                this.loadNotifications(true);
            });
        });

        // Filters
        document.getElementById('type-filter').addEventListener('change', () => this.applyFilters());
        document.getElementById('priority-filter').addEventListener('change', () => this.applyFilters());
        document.getElementById('search-filter').addEventListener('input', this.debounce(() => this.applyFilters(), 300));

        // Mark all as read
        document.getElementById('page-mark-all-read').addEventListener('click', () => this.markAllAsRead());

        // Push notification setup
        document.getElementById('push-notification-setup').addEventListener('click', () => this.togglePushNotifications());
        document.getElementById('push-toggle-btn').addEventListener('click', () => this.togglePushNotifications());
    }

    setupInfiniteScroll() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.isLoading && this.currentPage < this.lastPage) {
                    this.loadNotifications(false, this.currentPage + 1);
                }
            });
        }, { threshold: 0.1 });

        // Observe the loading indicator
        const loadingIndicator = document.getElementById('infinite-scroll-loading');
        if (loadingIndicator) {
            observer.observe(loadingIndicator);
        }
    }

    setupWebSocketListeners() {
        // Listen for real-time notification events
        if (typeof socket !== 'undefined') {
            socket.on('user.notification', (data) => {
                this.handleRealTimeNotification(data);
            });
        }
    }

    async setupPushNotifications() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.log('Push notifications not supported');
            return;
        }

        try {
            // Register service worker
            this.serviceWorker = await navigator.serviceWorker.register('/sw.js');
            console.log('Service Worker registered:', this.serviceWorker);

            // Check current subscription status
            await this.checkPushSubscriptionStatus();
        } catch (error) {
            console.error('Service Worker registration failed:', error);
        }
    }

    async checkPushSubscriptionStatus() {
        try {
            const response = await fetch(route('notifications.push.status'));
            const data = await response.json();
            
            this.updatePushStatus(data.data.subscribed);
            
            if (data.data.subscribed) {
                this.pushSubscription = data.data.subscription;
            }
        } catch (error) {
            console.error('Error checking push subscription status:', error);
        }
    }

    async togglePushNotifications() {
        if (!this.serviceWorker) {
            alert(translate('push_notifications_not_supported'));
            return;
        }

        try {
            if (this.pushSubscription) {
                await this.unsubscribeFromPush();
            } else {
                await this.subscribeToPush();
            }
        } catch (error) {
            console.error('Error toggling push notifications:', error);
            alert(translate('push_notification_error'));
        }
    }

    async subscribeToPush() {
        try {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                alert(translate('notification_permission_denied'));
                return;
            }

            const subscription = await this.serviceWorker.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(window.vapidPublicKey || 'your-vapid-public-key')
            });

            const response = await fetch(route('notifications.push.subscribe'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: this.arrayBufferToBase64(subscription.getKey('p256dh')),
                        auth: this.arrayBufferToBase64(subscription.getKey('auth'))
                    }
                })
            });

            if (response.ok) {
                this.pushSubscription = subscription;
                this.updatePushStatus(true);
                this.showNotification(translate('push_notifications_enabled'), 'success');
            } else {
                throw new Error('Subscription failed');
            }
        } catch (error) {
            console.error('Error subscribing to push notifications:', error);
            alert(translate('push_subscription_failed'));
        }
    }

    async unsubscribeFromPush() {
        try {
            const response = await fetch(route('notifications.push.unsubscribe'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                if (this.pushSubscription && this.pushSubscription.unsubscribe) {
                    await this.pushSubscription.unsubscribe();
                }
                this.pushSubscription = null;
                this.updatePushStatus(false);
                this.showNotification(translate('push_notifications_disabled'), 'info');
            }
        } catch (error) {
            console.error('Error unsubscribing from push notifications:', error);
        }
    }

    updatePushStatus(isSubscribed) {
        const statusElement = document.getElementById('push-status');
        const setupButton = document.getElementById('push-notification-setup');
        const toggleButton = document.getElementById('push-toggle-btn');
        const toggleIcon = document.getElementById('push-toggle-icon');

        if (isSubscribed) {
            statusElement.textContent = translate('enabled');
            setupButton.innerHTML = '<i class="fas fa-bell-slash"></i> ' + translate('disable_push_notifications');
            setupButton.className = 'btn btn-outline-danger';
            toggleButton.style.display = 'block';
            toggleIcon.className = 'fas fa-bell-slash';
        } else {
            statusElement.textContent = translate('disabled');
            setupButton.innerHTML = '<i class="fas fa-bell"></i> ' + translate('enable_push_notifications');
            setupButton.className = 'btn btn-outline-primary';
            toggleButton.style.display = 'none';
            toggleIcon.className = 'fas fa-bell';
        }
    }

    async loadNotifications(clearExisting = false, page = 1) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.currentPage = page;

        const filters = this.getActiveFilters();
        const params = new URLSearchParams({
            page: page,
            status: this.currentFilter === 'all' ? '' : this.currentFilter,
            ...filters
        });

        try {
            const response = await fetch(`${route('notifications.list')}?${params}`);
            const data = await response.json();

            if (data.success) {
                this.renderNotifications(data.data.data, clearExisting);
                this.lastPage = data.data.data.last_page;
                this.updateInfiniteScrollVisibility();
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showError(translate('error_loading_notifications'));
        } finally {
            this.isLoading = false;
        }
    }

    async loadStats() {
        try {
            const response = await fetch(route('notifications.stats'));
            const data = await response.json();

            if (data.success) {
                this.updateStatsDisplay(data.data);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    renderNotifications(notifications, clearExisting = false) {
        const container = document.querySelector(`.page-notifications-list[data-filter="${this.currentFilter}"]`);
        
        if (clearExisting) {
            container.innerHTML = '';
        }

        if (notifications.length === 0 && clearExisting) {
            container.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">${translate('no_notifications_found')}</h5>
                    <p class="text-muted">${translate('no_notifications_description')}</p>
                </div>
            `;
            return;
        }

        notifications.forEach(notification => {
            this.notifications.set(notification.id, notification);
            const element = this.createNotificationElement(notification);
            container.appendChild(element);
        });
    }

    createNotificationElement(notification) {
        const div = document.createElement('div');
        div.className = `notification-card card mb-3 ${notification.is_read ? 'read' : 'unread'} notification-priority-${notification.priority}`;
        div.dataset.notificationId = notification.id;

        const typeColors = {
            'info': 'primary',
            'alert': 'danger',
            'announcement': 'warning'
        };

        const priorityIcons = {
            'high': 'fas fa-exclamation-triangle',
            'medium': 'fas fa-info-circle',
            'low': 'fas fa-check-circle'
        };

        // Handle attachments
        let attachmentsHtml = '';
        if (notification.data && notification.data.attachments) {
            attachmentsHtml = `
                <div class="mt-2">
                    ${notification.data.attachments.map(attachment => `
                        <a href="${attachment.url}" class="notification-attachment" target="_blank">
                            <i class="fas fa-paperclip"></i> ${attachment.name}
                        </a>
                    `).join('')}
                </div>
            `;
        }

        div.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3">
                                <img src="${notification.icon || '/favicon.ico'}" 
                                     class="rounded-circle" 
                                     style="width: 40px; height: 40px; object-fit: cover;" 
                                     alt="notification icon">
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="mb-1 ${!notification.is_read ? 'fw-bold' : ''}">${notification.title || notification.name}</h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-${typeColors[notification.type] || 'secondary'} notification-type-badge me-2">
                                            ${notification.type}
                                        </span>
                                        <i class="${priorityIcons[notification.priority]} text-${notification.priority === 'high' ? 'danger' : notification.priority === 'medium' ? 'warning' : 'success'}"></i>
                                    </div>
                                </div>
                                <p class="mb-1 text-muted">${notification.body || notification.description}</p>
                                <small class="text-muted" title="${notification.created_at}">
                                    <i class="fas fa-clock"></i> ${notification.created_at_diff}
                                </small>
                                ${attachmentsHtml}
                            </div>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            ${notification.route ? `
                                <li><a class="dropdown-item" href="${notification.route}">
                                    <i class="fas fa-external-link-alt me-2"></i> ${translate('view')}
                                </a></li>
                            ` : ''}
                            <li><button class="dropdown-item" onclick="notificationManager.toggleReadStatus(${notification.id})">
                                <i class="fas fa-${notification.is_read ? 'envelope' : 'envelope-open'} me-2"></i> 
                                ${notification.is_read ? translate('mark_as_unread') : translate('mark_as_read')}
                            </button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="notificationManager.deleteNotification(${notification.id})">
                                <i class="fas fa-trash me-2"></i> ${translate('delete')}
                            </button></li>
                        </ul>
                    </div>
                </div>
            </div>
        `;

        // Add click handler for the card
        div.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown') && notification.route) {
                window.location.href = notification.route;
            }
        });

        return div;
    }

    async toggleReadStatus(notificationId) {
        const notification = this.notifications.get(notificationId);
        if (!notification) return;

        const endpoint = notification.is_read ? 'notifications.mark-as-unread' : 'notifications.mark-as-read';
        
        try {
            const response = await fetch(route(endpoint, { notification: notificationId }), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                notification.is_read = !notification.is_read;
                this.updateNotificationElement(notificationId);
                this.loadStats();
            }
        } catch (error) {
            console.error('Error toggling read status:', error);
        }
    }

    async deleteNotification(notificationId) {
        if (!confirm(translate('confirm_delete_notification'))) {
            return;
        }

        try {
            const response = await fetch(route('notifications.destroy', { notification: notificationId }), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                this.removeNotificationElement(notificationId);
                this.notifications.delete(notificationId);
                this.loadStats();
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    }

    async markAllAsRead() {
        if (!confirm(translate('confirm_mark_all_read'))) {
            return;
        }

        try {
            const response = await fetch(route('notifications.mark-all-as-read'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                // Update all notifications in memory
                this.notifications.forEach(notification => {
                    notification.is_read = true;
                });
                
                // Refresh the current view
                this.loadNotifications(true);
                this.loadStats();
                this.showNotification(translate('all_notifications_marked_read'), 'success');
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    updateNotificationElement(notificationId) {
        const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
        const notification = this.notifications.get(notificationId);
        
        if (element && notification) {
            element.className = element.className.replace(/\b(read|unread)\b/g, notification.is_read ? 'read' : 'unread');
            
            const title = element.querySelector('h6');
            if (title) {
                title.className = notification.is_read ? 'mb-1' : 'mb-1 fw-bold';
            }
        }
    }

    removeNotificationElement(notificationId) {
        const element = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (element) {
            element.remove();
        }
    }

    handleRealTimeNotification(data) {
        // Show browser notification if supported
        if (Notification.permission === 'granted') {
            new Notification(data.data.title, {
                body: data.data.message,
                icon: '/favicon.ico',
                tag: 'realtime-notification'
            });
        }

        // Update the notification counter and reload if on notifications page
        this.loadStats();
        
        // If we're on the "all" or "unread" tab, refresh the list
        if (this.currentFilter === 'all' || this.currentFilter === 'unread') {
            this.loadNotifications(true);
        }

        // Show in-app toast notification
        this.showNotification(data.data.title, 'info', data.data.message);
    }

    updateStatsDisplay(stats) {
        document.getElementById('total-notifications').textContent = stats.total || 0;
        document.getElementById('unread-notifications').textContent = stats.unread || 0;
        document.getElementById('read-notifications').textContent = stats.read || 0;
    }

    updateInfiniteScrollVisibility() {
        const loadingIndicator = document.getElementById('infinite-scroll-loading');
        if (this.currentPage < this.lastPage) {
            loadingIndicator.style.display = 'block';
        } else {
            loadingIndicator.style.display = 'none';
        }
    }

    getActiveFilters() {
        return {
            type: document.getElementById('type-filter').value,
            priority: document.getElementById('priority-filter').value,
            search: document.getElementById('search-filter').value
        };
    }

    applyFilters() {
        this.currentPage = 1;
        this.notifications.clear();
        this.loadNotifications(true);
    }

    showNotification(title, type = 'info', body = '') {
        // Use SweetAlert2 if available, otherwise fallback to alert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: body,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(title + (body ? ': ' + body : ''));
        }
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }
}

// Initialize the notification manager when DOM is ready
let notificationManager;
document.addEventListener('DOMContentLoaded', function() {
    notificationManager = new NotificationManager();
});

// Make it globally available for inline event handlers
window.notificationManager = notificationManager;
