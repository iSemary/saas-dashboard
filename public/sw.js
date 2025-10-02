// Service Worker for Push Notifications
const CACHE_NAME = 'saas-notifications-v1';

// Install event
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating...');
    event.waitUntil(self.clients.claim());
});

// Push event - handle incoming push notifications
self.addEventListener('push', event => {
    console.log('Service Worker: Push event received', event);

    if (!event.data) {
        console.log('Push event has no data');
        return;
    }

    try {
        const data = event.data.json();
        console.log('Push data:', data);

        const options = {
            body: data.body || 'You have a new notification',
            icon: data.icon || '/favicon.ico',
            badge: data.badge || '/favicon.ico',
            image: data.image,
            data: data.data || {},
            actions: [
                {
                    action: 'view',
                    title: 'View',
                    icon: '/assets/shared/images/icons/view.png'
                },
                {
                    action: 'dismiss',
                    title: 'Dismiss',
                    icon: '/assets/shared/images/icons/dismiss.png'
                }
            ],
            requireInteraction: data.priority === 'high',
            silent: false,
            vibrate: data.priority === 'high' ? [200, 100, 200] : [100],
            tag: data.tag || 'notification',
            renotify: true,
            timestamp: Date.now()
        };

        event.waitUntil(
            self.registration.showNotification(data.title || 'Notification', options)
        );
    } catch (error) {
        console.error('Error parsing push data:', error);
        
        // Fallback notification
        event.waitUntil(
            self.registration.showNotification('New Notification', {
                body: 'You have a new notification',
                icon: '/favicon.ico',
                badge: '/favicon.ico'
            })
        );
    }
});

// Notification click event
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event);

    event.notification.close();

    if (event.action === 'dismiss') {
        return;
    }

    const data = event.notification.data;
    let url = '/';

    if (event.action === 'view' && data.route) {
        url = data.route;
    } else if (data.route) {
        url = data.route;
    } else {
        url = '/notifications';
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            // Check if there's already a window/tab open with the target URL
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }

            // If no existing window/tab, open a new one
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

// Notification close event
self.addEventListener('notificationclose', event => {
    console.log('Notification closed:', event);
    
    // Optional: Track notification dismissals
    const data = event.notification.data;
    if (data.id) {
        // You could send analytics data here
        console.log('Notification dismissed:', data.id);
    }
});

// Background sync (optional - for offline functionality)
self.addEventListener('sync', event => {
    console.log('Background sync:', event);
    
    if (event.tag === 'notification-sync') {
        event.waitUntil(syncNotifications());
    }
});

// Function to sync notifications when back online
async function syncNotifications() {
    try {
        // Fetch any pending notifications
        const response = await fetch('/api/notifications/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'include'
        });

        if (response.ok) {
            const notifications = await response.json();
            
            // Show any notifications that were missed while offline
            notifications.forEach(notification => {
                self.registration.showNotification(notification.title, {
                    body: notification.body,
                    icon: notification.icon || '/favicon.ico',
                    data: notification.data || {}
                });
            });
        }
    } catch (error) {
        console.error('Error syncing notifications:', error);
    }
}

// Message event - handle messages from the main thread
self.addEventListener('message', event => {
    console.log('Service Worker received message:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Fetch event (optional - for caching strategies)
self.addEventListener('fetch', event => {
    // Only handle same-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // Handle notification-related API calls
    if (event.request.url.includes('/api/notifications')) {
        event.respondWith(
            fetch(event.request).catch(() => {
                // Return cached response or offline message
                return new Response(
                    JSON.stringify({ error: 'Offline', message: 'You are currently offline' }),
                    {
                        status: 503,
                        headers: { 'Content-Type': 'application/json' }
                    }
                );
            })
        );
    }
});
