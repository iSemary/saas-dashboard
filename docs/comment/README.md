# Comment Module Documentation

## Overview

The Comment module provides a flexible commenting system for various entities across the platform. It enables users to add, edit, and delete comments on different resources such as tickets, tasks, documents, and other entities. The module supports nested comments, mentions, and comment threading.

## Architecture

### Module Structure

```
Comment/
├── Config/              # Module configuration
├── Entities/            # Comment entities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Notifications/       # Comment-related notifications
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Routes/              # API and web routes
├── Services/            # Business logic services
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Comments
- `id` - Primary key
- `commentable_type` - Entity type (polymorphic)
- `commentable_id` - Entity ID (polymorphic)
- `user_id` - User who created comment
- `parent_id` - Parent comment ID (for nested comments)
- `content` - Comment content
- `status` - Comment status (pending, approved, rejected, spam)
- `created_at`, `updated_at` - Timestamps

#### Comment Likes
- `id` - Primary key
- `comment_id` - Associated comment
- `user_id` - User who liked
- `created_at` - Timestamp

#### Comment Mentions
- `id` - Primary key
- `comment_id` - Associated comment
- `user_id` - Mentioned user
- `read_at` - Read timestamp
- `created_at` - Timestamp

## API Endpoints

### Comments

**List Comments:** `GET /api/tenant/comments`

**Query Parameters:**
- `commentable_type` - Filter by entity type
- `commentable_id` - Filter by entity ID
- `user_id` - Filter by user
- `status` - Filter by status
- `parent_id` - Filter by parent comment

**Create Comment:** `POST /api/tenant/comments`
**Get Comment:** `GET /api/tenant/comments/{id}`
**Update Comment:** `PUT /api/tenant/comments/{id}`
**Delete Comment:** `DELETE /api/tenant/comments/{id}`

### Comment Likes

**Like Comment:** `POST /api/tenant/comments/{id}/like`
**Unlike Comment:** `DELETE /api/tenant/comments/{id}/like`
**Get Comment Likes:** `GET /api/tenant/comments/{id}/likes`

### Comment Mentions

**Get Mentions:** `GET /api/tenant/comments/{id}/mentions`
**Mark Mention as Read:** `POST /api/tenant/comments/{id}/mentions/{mentionId}/read`

## Services

### CommentService
- Comment CRUD operations
- Nested comment handling
- Comment validation
- Comment status management

### CommentNotificationService
- Mention notifications
- Comment reply notifications
- Notification dispatching

## Repositories

### CommentRepository
- Comment data access
- Polymorphic queries
- Nested comment queries
- Comment filtering and searching

### CommentLikeRepository
- Like data access
- User like queries
- Like counting

### CommentMentionRepository
- Mention data access
- User mention queries
- Unread mention queries

## Comment Features

### Nested Comments
- Support for threaded discussions
- Parent-child comment relationships
- Unlimited nesting depth
- Comment tree rendering

### Mentions
- User mentions in comments (@username)
- Automatic mention detection
- Mention notifications
- Read/unread status tracking

### Likes
- Like/unlike comments
- Like count display
- User like status
- Like notifications

### Comment Status
- `pending` - Awaiting moderation
- `approved` - Approved and visible
- `rejected` - Rejected by moderator
- `spam` - Marked as spam

## Configuration

### Module Configuration

Module configuration in `Config/comment.php`:

```php
return [
    'comments' => [
        'require_approval' => false,
        'allow_anonymous' => false,
        'max_depth' => 5,
        'max_length' => 5000,
    ],
    'mentions' => [
        'enabled' => true,
        'notification' => true,
    ],
    'likes' => [
        'enabled' => true,
        'notification' => true,
    ],
];
```

## Usage Examples

### Creating a Comment

```php
use Modules\Comment\Services\CommentService;

$commentService = app(CommentService::class);

$comment = $commentService->create([
    'commentable_type' => App\Models\Ticket::class,
    'commentable_id' => $ticketId,
    'user_id' => $userId,
    'content' => 'This is a comment with @john mention.',
]);
```

### Creating a Nested Comment

```php
$comment = $commentService->create([
    'commentable_type' => App\Models\Ticket::class,
    'commentable_id' => $ticketId,
    'user_id' => $userId,
    'parent_id' => $parentCommentId,
    'content' => 'This is a reply to the parent comment.',
]);
```

### Getting Comments for an Entity

```php
$comments = $commentService->getCommentsForEntity(
    App\Models\Ticket::class,
    $ticketId
);
```

## Permissions

Comment module permissions follow the pattern: `comment.{resource}.{action}`

- `comment.view` - View comments
- `comment.create` - Create comments
- `comment.edit` - Edit own comments
- `comment.edit_any` - Edit any comment
- `comment.delete` - Delete own comments
- `comment.delete_any` - Delete any comment
- `comment.moderate` - Moderate comments (approve/reject)
- `comment.like` - Like comments

## Notifications

The module sends notifications for:
- New comment on entity you're following
- Reply to your comment
- Mentions in comments
- Likes on your comments

## Business Rules

- Users can only edit their own comments (unless they have edit_any permission)
- Users can only delete their own comments (unless they have delete_any permission)
- Comment depth is limited by configuration
- Comment length is limited by configuration
- Mentions must reference valid users
- Anonymous comments are disabled by default

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Comment/Tests --testdox
```

Test coverage includes:
- Unit tests for comment creation
- Feature tests for API endpoints
- Nested comment tests
- Mention detection tests
- Like/unlike tests

## Related Documentation

- [Comment System Guide](../../backend/documentation/comment/guide.md)
