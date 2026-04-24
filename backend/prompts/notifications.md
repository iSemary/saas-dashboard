You are working on a Laravel SaaS app with a generic Notifications module.  
Currently, notifications are stored in the database and displayed on a notifications page using Blade + JavaScript.  

### Task
I want you to refactor and enhance the Notifications module with the following:  

1. **Push Notifications (Chrome / Browser)**
   - Implement push notifications using Web Push + Service Workers.  
   - Use Laravel WebPush package (or built-in Laravel notifications with WebPush driver).  
   - Register and manage push subscriptions per user.  
   - When a notification is created, push it instantly to subscribed users.  

2. **WebSocket Integration**
   - Integrate Laravel WebSockets (or Pusher as fallback).  
   - Broadcast new notifications via WebSockets.  
   - Frontend should listen for real-time events and update the notification bell counter and list dynamically without refresh.  

3. **Notifications Page (Refactor)**
   - Improve the Blade + JS notifications page.  
   - Add tabs/filters: "All", "Unread", "Read".  
   - Infinite scroll or lazy loading for long lists.  
   - Mark as read/unread with AJAX (no full page reload).  
   - Support in-page real-time updates from WebSockets.  
   - Display attachments (if present) and allow click-to-open.  
   - Enhance UI with Tailwind (notification cards, timestamps, icons).  

4. **Database / Backend Enhancements**
   - Ensure notifications table supports: `id`, `user_id`, `title`, `body`, `data (JSON)`, `is_read`, `created_at`.  
   - Add `notification_channels` table to track subscriptions (web, push, email).  
   - Support structured payloads (e.g., `{type: 'ticket', ticket_id: 123}`).  

5. **Deliverables**
   - Service Worker file for push notifications.  
   - Updated Laravel Notification class for Web + Push + WebSocket.  
   - Blade view refactor for notifications page.  
   - Example JavaScript code for listening to WebSocket events and Service Worker push events.  
   - Routes + Controller updates for subscription handling (subscribe/unsubscribe push).  

Keep the implementation modular and reusable, so I can trigger notifications from anywhere in the system (Tickets, Comments, etc.).  


