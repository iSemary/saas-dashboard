# Enhanced Notifications Module

A comprehensive notification system for Laravel SaaS applications with support for push notifications, WebSocket real-time updates, and an enhanced user interface.

## Features

### 🔔 Push Notifications
- Browser push notifications using Web Push API
- Service Worker integration for offline support
- VAPID authentication for secure delivery
- Subscription management per user

### 🌐 WebSocket Integration
- Real-time notification delivery via WebSockets
- Automatic UI updates without page refresh
- Live notification counter updates
- Toast notifications for immediate feedback

### 📱 Enhanced User Interface
- Modern, responsive design with Tailwind CSS
- Tabbed interface (All, Unread, Read)
- Advanced filtering (type, priority, search)
- Infinite scroll for large notification lists
- Interactive notification cards with hover effects

### 🗄️ Database Enhancements
- Enhanced notifications table with new fields
- Notification channels table for subscription management
- Support for structured data payloads
- Proper indexing for performance

## Installation & Setup

### 1. Database Migration

Run the migrations to set up the enhanced database structure:

```bash
php artisan migrate --path=modules/Notification/Database/migrations/shared
```

### 2. WebPush Configuration

Add the following environment variables to your `.env` file:

```env
# VAPID Keys for Push Notifications (generate using: php artisan webpush:vapid)
VAPID_SUBJECT=mailto:your-email@example.com
VAPID_PUBLIC_KEY=your-vapid-public-key
VAPID_PRIVATE_KEY=your-vapid-private-key
```

Generate VAPID keys:

```bash
php artisan webpush:vapid
```

### 3. Service Worker Registration

The service worker is automatically available at `/sw.js`. Make sure your web server serves this file correctly.

### 4. WebSocket Configuration

Ensure your WebSocket server is running and configured properly. The system uses the existing WebSocket infrastructure.

## Usage Examples

### Basic Notification

```php
use Modules\Notification\Notifications\GenericNotification;
use Modules\Auth\Entities\User;

$user = User::find(1);

// Simple notification
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
// Using the helper method
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

### Notification with Attachments

```php
$notification = new GenericNotification(
    'Document Ready',
    'Your requested document is ready for download.',
    [
        'attachments' => [
            ['name' => 'report.pdf', 'url' => '/downloads/report.pdf'],
            ['name' => 'summary.xlsx', 'url' => '/downloads/summary.xlsx']
        ]
    ],
    'info',
    'medium'
);

$user->notify($notification);
```

## API Endpoints

### Notification Management

- `GET /notifications` - View notifications page
- `GET /notifications/list` - Get paginated notifications (AJAX)
- `GET /notifications/stats` - Get notification statistics
- `POST /notifications/mark-all-as-read` - Mark all notifications as read
- `POST /notifications/mark-as-read/{id}` - Mark specific notification as read
- `POST /notifications/mark-as-unread/{id}` - Mark specific notification as unread
- `DELETE /notifications/{id}` - Delete notification

### Push Notification Management

- `POST /notifications/push/subscribe` - Subscribe to push notifications
- `POST /notifications/push/unsubscribe` - Unsubscribe from push notifications
- `GET /notifications/push/status` - Get subscription status
- `POST /notifications/push/test` - Send test push notification

## Frontend Integration

### JavaScript API

The enhanced notification system provides a JavaScript API for frontend integration:

```javascript
// Access the global notification manager
notificationManager.toggleReadStatus(notificationId);
notificationManager.deleteNotification(notificationId);
notificationManager.markAllAsRead();
notificationManager.togglePushNotifications();
```

### WebSocket Events

Listen for real-time notification events:

```javascript
socket.on('user.notification', function(data) {
    // Handle real-time notification
    console.log('New notification:', data);
});
```

### Push Notification Handling

The service worker automatically handles push notifications and provides:

- Automatic notification display
- Click handling with URL navigation
- Offline support
- Background sync capabilities

## Customization

### Custom Notification Types

Create custom notification classes by extending `BaseNotification`:

```php
<?php

namespace App\Notifications;

use Modules\Notification\Notifications\BaseNotification;

class CustomNotification extends BaseNotification
{
    public function __construct($customData)
    {
        parent::__construct(
            'Custom Title',
            'Custom message body',
            $customData,
            'custom',
            'medium'
        );
    }
}
```

### UI Customization

The notification interface can be customized by modifying:

- `/resources/views/user/notifications/index.blade.php` - Main notification page
- `/public/assets/shared/js/notifications/enhanced-index.js` - JavaScript functionality
- CSS styles in the view file

### Database Customization

The notification system supports custom data structures through the `data` JSON field:

```php
$notification = new GenericNotification(
    'Custom Notification',
    'This notification has custom data',
    [
        'custom_field' => 'value',
        'metadata' => ['key' => 'value'],
        'attachments' => [...]
    ]
);
```

## Performance Considerations

### Database Indexing

The system includes optimized database indexes for:
- User-based queries
- Read status filtering
- Date-based sorting
- Type-based filtering

### Caching

Consider implementing caching for:
- Notification counts
- User subscription status
- Frequently accessed notifications

### Pagination

The system uses pagination for large notification lists:
- Default: 10 notifications per page
- Infinite scroll for seamless user experience
- Configurable page sizes

## Security

### Push Notification Security

- VAPID authentication ensures secure push delivery
- Subscription data is encrypted and stored securely
- User consent required for push notifications

### WebSocket Security

- Private channels ensure notifications are only sent to authorized users
- CSRF protection on all API endpoints
- Authentication required for all notification operations

## Troubleshooting

### Common Issues

1. **Push notifications not working**
   - Check VAPID keys configuration
   - Ensure HTTPS is enabled (required for push notifications)
   - Verify service worker registration

2. **WebSocket connection issues**
   - Check WebSocket server status
   - Verify broadcasting configuration
   - Ensure proper channel authentication

3. **Notifications not displaying**
   - Check database migrations
   - Verify notification observer is registered
   - Check JavaScript console for errors

### Debug Mode

Enable debug logging by adding to your `.env`:

```env
LOG_LEVEL=debug
```

This will log notification events, push attempts, and WebSocket communications.

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

## Contributing

When contributing to the notification system:

1. Follow the existing code structure
2. Add tests for new features
3. Update documentation
4. Ensure backward compatibility
5. Test across different browsers

## License

This enhanced notification system is part of the SaaS dashboard and follows the same license terms.
