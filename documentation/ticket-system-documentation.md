# Ticket System Documentation

## Overview

The Ticket System is a comprehensive solution for managing support tickets, feature requests, and issues within the SaaS application. It includes a reusable Comments module that can be used across different entities like tickets, posts, and other objects.

## Architecture

### Modules Structure

The system consists of two main modules:

1. **Ticket Module** (`modules/Ticket/`)
   - Handles ticket management, status tracking, and SLA monitoring
   - Located in tenant database for multi-tenancy support

2. **Comment Module** (`modules/Comment/`)
   - Reusable commenting system with reactions and attachments
   - Located in shared database for cross-tenant functionality

### Design Patterns

Both modules follow the established MVC architecture:
- **Controller** → **Service** → **Interface** → **Repository**
- Eloquent models with proper relationships and traits
- Service providers for dependency injection
- Middleware for permissions and access control

## Database Schema

### Ticket Module Tables (Tenant Database)

#### `tickets`
```sql
- id (primary key)
- ticket_number (unique, auto-generated)
- title
- description (text)
- html_content (longtext, rich content)
- status (enum: open, in_progress, on_hold, resolved, closed)
- priority (enum: low, medium, high, urgent)
- created_by (foreign key to users)
- assigned_to (foreign key to users, nullable)
- brand_id (foreign key to brands, nullable)
- tags (json array)
- due_date (timestamp, nullable)
- resolved_at (timestamp, nullable)
- closed_at (timestamp, nullable)
- sla_data (json, SLA tracking information)
- metadata (json, additional data)
- timestamps, soft deletes
```

#### `ticket_status_logs`
```sql
- id (primary key)
- ticket_id (foreign key to tickets)
- old_status (nullable for initial status)
- new_status
- changed_by (foreign key to users)
- comment (text, nullable)
- time_in_previous_status (integer, seconds)
- metadata (json)
- timestamps
```

### Comment Module Tables (Shared Database)

#### `comments`
```sql
- id (primary key)
- parent_id (foreign key to comments, nullable for threading)
- comment (longtext)
- user_id (foreign key to users)
- seen (boolean, read status)
- object_id (polymorphic relation)
- object_model (polymorphic relation)
- metadata (json)
- timestamps, soft deletes
```

#### `comment_attachments`
```sql
- id (primary key)
- comment_id (foreign key to comments)
- attachment_url (file path)
- thumbnail_url (nullable, for images)
- original_name
- mime_type
- file_size (bigint)
- user_id (foreign key to users)
- metadata (json)
- timestamps, soft deletes
```

#### `comment_reactions`
```sql
- id (primary key)
- comment_id (foreign key to comments)
- reaction_type (enum: like, love, dislike, laugh, angry, sad)
- user_id (foreign key to users)
- timestamps
- unique constraint on (comment_id, user_id)
```

## Features

### Ticket Management

#### Core Features
- **Ticket Creation**: Rich text editor support with HTML content
- **Status Management**: 5-stage workflow (open → in_progress → on_hold → resolved → closed)
- **Priority Levels**: Low, Medium, High, Urgent with visual indicators
- **Assignment System**: Assign tickets to users with notification support
- **Due Dates**: Set and track ticket deadlines with overdue indicators
- **Tags**: Flexible tagging system for categorization
- **Brand Association**: Link tickets to specific brands for multi-brand support

#### Advanced Features
- **SLA Tracking**: Automatic time tracking for each status
- **Status History**: Complete audit trail of all status changes
- **Bulk Operations**: Mass assign, status update, priority change, delete
- **Search & Filtering**: Advanced filtering by status, priority, assignee, date range
- **Auto-refresh**: Real-time updates in list and Kanban views

### Kanban Board

#### Features
- **Drag & Drop**: Move tickets between status columns
- **Real-time Updates**: Auto-refresh every 30 seconds
- **Filtering**: Filter by assignee, priority, search terms
- **Bulk Actions**: Perform actions on all tickets in a status column
- **Visual Indicators**: Priority colors, overdue warnings, comment counts
- **Responsive Design**: Works on desktop and mobile devices

#### User Experience
- **AlpineJS Integration**: Smooth interactions without page reloads
- **Loading States**: Visual feedback during operations
- **Error Handling**: Graceful error messages with retry options
- **Keyboard Shortcuts**: Quick actions for power users

### Comment System

#### Core Features
- **Threaded Comments**: Nested reply system with unlimited depth
- **Rich Content**: Support for HTML content and formatting
- **File Attachments**: Upload multiple files with preview
- **Reactions**: 6 emoji reactions (like, love, dislike, laugh, angry, sad)
- **Read Status**: Track seen/unseen comments
- **Polymorphic Relations**: Reusable across different entities

#### File Handling
- **Multiple Formats**: Images, documents, videos supported
- **Thumbnail Generation**: Automatic thumbnails for images
- **File Validation**: Size limits and type restrictions
- **Secure Storage**: Private file access with proper permissions

### Notification System

#### Email Notifications
- **Ticket Created**: Notify assignees of new tickets
- **Status Changed**: Alert stakeholders of status updates
- **Comment Added**: Notify participants of new comments/replies
- **Overdue Tickets**: Automated reminders for due dates

#### In-App Notifications
- **Real-time Updates**: Instant notifications for ticket changes
- **Notification Center**: Centralized notification management
- **Read/Unread Status**: Track notification status
- **Action Links**: Direct links to relevant tickets/comments

## User Interface

### Views Structure

#### Ticket Views
- **Index** (`/landlord/tickets`): DataTables with filtering and bulk actions
- **Kanban** (`/landlord/tickets/kanban`): Drag & drop board interface
- **Show** (`/landlord/tickets/{id}`): Detailed ticket view with comments
- **Create/Edit** (`/landlord/tickets/create`): Form-based ticket management

#### Comment Views
- **Partials**: Reusable comment components
- **Modals**: Quick action dialogs
- **AJAX Forms**: Seamless comment submission

### JavaScript Architecture

#### Libraries Used
- **AlpineJS**: Reactive data binding and interactions
- **jQuery**: DOM manipulation and AJAX requests
- **DataTables**: Advanced table functionality
- **Bootstrap**: Modal dialogs and UI components
- **Toastr**: User-friendly notifications

#### Key Features
- **Auto-refresh**: Periodic data updates
- **Drag & Drop**: Sortable.js integration for Kanban
- **File Upload**: Progress indicators and validation
- **Real-time Updates**: WebSocket support (optional)

## API Endpoints

### Ticket Endpoints

#### REST API
```
GET    /api/v1/tickets                 - List tickets
POST   /api/v1/tickets                 - Create ticket
GET    /api/v1/tickets/{id}            - Show ticket
PUT    /api/v1/tickets/{id}            - Update ticket
DELETE /api/v1/tickets/{id}            - Delete ticket
```

#### Specialized Endpoints
```
GET    /landlord/tickets/kanban-data   - Kanban board data
PATCH  /landlord/tickets/{id}/status   - Update status
PATCH  /landlord/tickets/{id}/assign   - Assign ticket
PATCH  /landlord/tickets/{id}/close    - Close ticket
PATCH  /landlord/tickets/{id}/reopen   - Reopen ticket
PATCH  /landlord/tickets/bulk-update   - Bulk operations
GET    /landlord/tickets/stats         - Statistics
GET    /landlord/tickets/metrics       - Reporting data
```

### Comment Endpoints

#### REST API
```
GET    /api/v1/comments                - List comments
POST   /api/v1/comments                - Create comment
GET    /api/v1/comments/{id}           - Show comment
PUT    /api/v1/comments/{id}           - Update comment
DELETE /api/v1/comments/{id}           - Delete comment
```

#### Specialized Endpoints
```
GET    /landlord/comments/object/{id}/{model}  - Get object comments
POST   /landlord/comments/{id}/reply           - Add reply
POST   /landlord/comments/{id}/reaction        - Toggle reaction
PATCH  /landlord/comments/{id}/seen            - Mark as seen
GET    /landlord/comments/unseen-count         - Unseen count
```

## Permissions

### Ticket Permissions
- `read.tickets` - View tickets
- `create.tickets` - Create new tickets
- `update.tickets` - Edit tickets, change status, assign
- `delete.tickets` - Delete tickets
- `restore.tickets` - Restore deleted tickets

### Comment Permissions
- `read.comments` - View comments
- `create.comments` - Add comments and replies
- `update.comments` - Edit own comments
- `delete.comments` - Delete own comments
- `restore.comments` - Restore deleted comments

## Installation & Setup

### 1. Module Registration

Add to `config/modules.php`:
```php
'modules' => [
    // ... existing modules
    'Ticket',
    'Comment',
],
```

### 2. Run Migrations

```bash
# Run migrations for both modules
php artisan migrate

# The system will automatically detect:
# - Ticket migrations → tenant database
# - Comment migrations → shared database
```

### 3. Seed Sample Data

```bash
# Seed tickets and comments
php artisan db:seed --class="Modules\Ticket\Database\Seeders\TicketSeeder"
php artisan db:seed --class="Modules\Comment\Database\Seeders\CommentSeeder"
```

### 4. Configure Permissions

```bash
# Assign permissions to roles
php artisan permission:sync
```

## Configuration

### File Upload Settings

Configure in `.env`:
```env
# File upload limits
MAX_FILE_SIZE=10240  # 10MB in KB
ALLOWED_FILE_TYPES=jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt

# Storage settings
FILESYSTEM_DISK=local
```

### Notification Settings

Configure in `config/mail.php` and notification channels:
```php
// Email notifications
'ticket_notifications' => true,
'comment_notifications' => true,

// In-app notifications
'realtime_notifications' => true,
```

### SLA Configuration

Configure SLA rules in `config/ticket.php`:
```php
'sla' => [
    'urgent' => 4,    // 4 hours
    'high' => 24,     // 24 hours
    'medium' => 72,   // 72 hours
    'low' => 168,     // 1 week
],
```

## Usage Examples

### Creating a Ticket

```php
use Modules\Ticket\Services\TicketService;

$ticketService = app(TicketService::class);

$ticket = $ticketService->create([
    'title' => 'Login Issue',
    'description' => 'Users cannot log in',
    'priority' => 'high',
    'assigned_to' => 1,
    'due_date' => now()->addDays(2),
    'tags' => ['bug', 'urgent']
]);
```

### Adding a Comment

```php
use Modules\Comment\Services\CommentService;

$commentService = app(CommentService::class);

$comment = $commentService->create([
    'comment' => 'This issue has been resolved',
    'object_id' => $ticket->id,
    'object_model' => 'Modules\Ticket\Entities\Ticket',
    'user_id' => auth()->id()
]);
```

### Using Comments in Other Modules

```php
// In any model that needs comments
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Comment\Entities\Comment;

class Post extends Model
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable', 'object_model', 'object_id');
    }
}

// In views
@include('landlord.comment.comments.partials.comments', [
    'comments' => $post->comments()->with(['user', 'attachments', 'reactions'])->get()
])
```

## Testing

### Feature Tests

Run the comprehensive test suite:
```bash
# Test ticket functionality
php artisan test tests/Feature/TicketSystemTest.php

# Test comment functionality
php artisan test tests/Feature/CommentSystemTest.php
```

### Manual Testing Checklist

#### Ticket System
- [ ] Create ticket with all fields
- [ ] Update ticket status via Kanban drag & drop
- [ ] Assign/reassign tickets
- [ ] Add comments with attachments
- [ ] Test bulk operations
- [ ] Verify SLA tracking
- [ ] Check notification delivery

#### Comment System
- [ ] Add top-level comments
- [ ] Reply to comments (threading)
- [ ] Upload file attachments
- [ ] Add/remove reactions
- [ ] Mark comments as seen
- [ ] Test polymorphic relations

## Troubleshooting

### Common Issues

#### File Upload Issues
```bash
# Check storage permissions
chmod -R 755 storage/app/public

# Clear cache
php artisan cache:clear
php artisan config:clear
```

#### Migration Issues
```bash
# Check database connections
php artisan tinker
DB::connection('landlord')->getPdo()
DB::connection('tenant')->getPdo()

# Re-run specific migrations
php artisan migrate --path=modules/Ticket/Database/migrations/tenant
```

#### Permission Issues
```bash
# Sync permissions
php artisan permission:cache-reset
php artisan permission:sync
```

### Performance Optimization

#### Database Indexes
```sql
-- Add indexes for better performance
CREATE INDEX idx_tickets_status_priority ON tickets(status, priority);
CREATE INDEX idx_tickets_assigned_to ON tickets(assigned_to);
CREATE INDEX idx_comments_object ON comments(object_id, object_model);
```

#### Caching
```php
// Cache frequently accessed data
Cache::remember('ticket_stats', 300, function () {
    return $ticketService->getTicketStats();
});
```

## Future Enhancements

### Planned Features
- [ ] Advanced SLA rules with business hours
- [ ] Ticket templates for common issues
- [ ] Custom fields for tickets
- [ ] Integration with external tools (Slack, Teams)
- [ ] Advanced reporting and analytics
- [ ] Mobile app support
- [ ] AI-powered ticket categorization
- [ ] Customer portal for ticket submission

### API Improvements
- [ ] GraphQL API support
- [ ] Webhook notifications
- [ ] Rate limiting per user/tenant
- [ ] API versioning strategy

## Support

For technical support or feature requests:
- Create a ticket using the system itself
- Check the troubleshooting section
- Review the test cases for usage examples
- Consult the API documentation for integration details

---

**Last Updated:** October 2, 2025  
**Version:** 1.0.0  
**Compatibility:** Laravel 10+, PHP 8.1+
