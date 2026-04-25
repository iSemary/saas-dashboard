# Notification Module Documentation

## Overview

The Notification module provides a comprehensive notification system for the SaaS platform with support for push notifications, WebSocket real-time updates, and an enhanced user interface. It enables sending notifications to users through multiple channels including database storage, push notifications, and real-time WebSocket updates.

## Architecture

### Module Structure

```
Notification/
├── Config/              # Module configuration
├── Entities/            # Notification entities
├── Examples/            # Usage examples
├── Http/                # HTTP controllers
├── Notifications/       # Notification classes
├── Observers/           # Model observers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Routes/              # API routes
├── Services/            # Business logic services
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Notifications
- `id` - Primary key
- `user_id` - Recipient user ID
- `type` - Notification type
- `title` - Notification title
- `message` - Notification message
- `data` - Notification data (JSON)
- `read_at` - Read timestamp
- `priority` - Priority level (low, medium, high)
- `created_at`, `updated_at` - Timestamps

#### Notification Channels
- `id` - Primary key
- `user_id` - User ID
- `channel_type` - Channel type (push, email, sms, websocket)
- `endpoint` - Channel endpoint
- `subscription_data` - Subscription data (JSON)
- `status` - Channel status
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Notification Management

**List Notifications:** `GET /notifications`
**Get Notification List (AJAX):** `GET /notifications/list`
**Get Notification Stats:** `GET /notifications/stats`
**Mark All as Read:** `POST /notifications/mark-all-as-read`
**Mark as Read:** `POST /notifications/mark-as-read/{id}`
**Mark as Unread:** `POST /notifications/mark-as-unread/{id}`
**Delete Notification:** `DELETE /notifications/{id}`

### Push Notification Management

**Subscribe to Push:** `POST /notifications/push/subscribe`
**Unsubscribe from Push:** `POST /notifications/push/unsubscribe`
**Get Subscription Status:** `GET /notifications/push/status`
**Send Test Push:** `POST /notifications/push/test`

## Services

### NotificationService
- Notification creation and dispatch
- Channel management
- Push notification handling
- WebSocket notification broadcasting

### PushNotificationService
- VAPID key management
- Subscription management
- Push notification delivery
- Service worker integration

## Repositories

### NotificationRepository
- Notification data access
- User notification queries
- Read/unread filtering
- Priority-based queries

### NotificationChannelRepository
- Channel subscription management
- User channel queries
- Subscription status tracking

## Notification Types

### Generic Notification
Standard notification with title, message, and data payload.

### Ticket Notification
Specialized notification for ticket-related events.

### Alert Notification
High-priority notification for system alerts and warnings.

## Features

### Push Notifications
- Browser push notifications using Web Push API
- Service Worker integration for offline support
- VAPID authentication for secure delivery
- Subscription management per user

### WebSocket Integration
- Real-time notification delivery via WebSockets
- Automatic UI updates without page refresh
- Live notification counter updates
- Toast notifications for immediate feedback

### Enhanced User Interface
- Modern, responsive design with Tailwind CSS
- Tabbed interface (All, Unread, Read)
- Advanced filtering (type, priority, search)
- Infinite scroll for large notification lists
- Interactive notification cards with hover effects

## Usage Examples

### Basic Notification

```php
use Modules\Notification\Notifications\GenericNotification;
use Modules\Auth\Entities\User;

$user = User::find(1);

$notification = new GenericNotification(
    'Welcome!',
    'Thank you for joining our platform.',
    ['user_id' => $user->id],
    'info',
    'low'
);

$user->notify($notification);
```

### Ticket Notification

```php
$notification = GenericNotification::ticket(
    'New Ticket Assigned',
    'Ticket #123 has been assigned to you.',
    123,
    route('tickets.show', 123)
);

$user->notify($notification);
```

### Alert Notification

```php
$notification = GenericNotification::alert(
    'System Maintenance',
    'The system will be under maintenance from 2 AM to 4 AM.',
    ['maintenance_window' => '2023-12-01 02:00:00']
);

$user->notify($notification);
```

## Configuration

### Environment Variables

```env
# VAPID Keys for Push Notifications
VAPID_SUBJECT=mailto:your-email@example.com
VAPID_PUBLIC_KEY=your-vapid-public-key
VAPID_PRIVATE_KEY=your-vapid-private-key
```

Generate VAPID keys:
```bash
php artisan webpush:vapid
```

### Module Configuration

Module configuration in `Config/notification.php`:

```php
return [
    'push' => [
        'enabled' => true,
        'vapid' => [
            'subject' => env('VAPID_SUBJECT'),
            'public_key' => env('VAPID_PUBLIC_KEY'),
            'private_key' => env('VAPID_PRIVATE_KEY'),
        ],
    ],
    'websocket' => [
        'enabled' => true,
        'channel' => 'user.notifications',
    ],
    'ui' => [
        'per_page' => 10,
        'infinite_scroll' => true,
    ],
];
```

## Frontend Integration

### JavaScript API

```javascript
// Access the global notification manager
notificationManager.toggleReadStatus(notificationId);
notificationManager.deleteNotification(notificationId);
notificationManager.markAllAsRead();
notificationManager.togglePushNotifications();
```

### WebSocket Events

```javascript
socket.on('user.notification', function(data) {
    // Handle real-time notification
    console.log('New notification:', data);
});
```

## Permissions

Notification module permissions follow the pattern: `notifications.{resource}.{action}`

- `notifications.view` - View notifications
- `notifications.manage` - Manage notifications
- `notifications.delete` - Delete notifications
- `notifications.push.subscribe` - Subscribe to push notifications
- `notifications.push.manage` - Manage push subscriptions

## Security

### Push Notification Security
- VAPID authentication ensures secure push delivery
- Subscription data is encrypted and stored securely
- User consent required for push notifications

### WebSocket Security
- Private channels ensure notifications are only sent to authorized users
- CSRF protection on all API endpoints
- Authentication required for all notification operations

## Browser Support

### Push Notifications
- Chrome 50+
- Firefox 44+
- Safari 16+
- Edge 17+

### WebSocket Support
- All modern browsers
- Fallback to polling for older browsers

### Service Worker Support
- Chrome 40+
- Firefox 44+
- Safari 11.1+
- Edge 17+

## Related Documentation

- [Notification Module README](../../backend/modules/Notification/README.md)
