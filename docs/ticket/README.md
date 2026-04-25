# Ticket Module Documentation

## Overview

The Ticket module provides comprehensive ticket management functionality including ticket creation, assignment, status tracking, priority management, and resolution tracking. It enables support teams to manage customer inquiries, bug reports, and internal requests efficiently.

## Architecture

### Module Structure

```
Ticket/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # Ticket entities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Notifications/       # Ticket notifications
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Routes/              # API and web routes
├── Services/            # Business logic services
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Tickets
- `id` - Primary key
- `ticket_number` - Unique ticket number
- `subject` - Ticket subject
- `description` - Ticket description
- `category` - Ticket category
- `priority` - Priority level (low, medium, high, urgent)
- `status` - Ticket status (open, in_progress, pending, resolved, closed)
- `assigned_to` - Assigned user ID
- `created_by` - User who created ticket
- `ticketable_type` - Related entity type (polymorphic)
- `ticketable_id` - Related entity ID (polymorphic)
- `resolved_at` - Resolution timestamp
- `closed_at` - Closure timestamp
- `created_at`, `updated_at` - Timestamps

#### Ticket Comments
- `id` - Primary key
- `ticket_id` - Associated ticket
- `user_id` - User who commented
- `content` - Comment content
- `is_internal` - Internal comment flag
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Tickets

**List Tickets:** `GET /api/tenant/tickets`

**Query Parameters:**
- `status` - Filter by status
- `priority` - Filter by priority
- `category` - Filter by category
- `assigned_to` - Filter by assignee
- `created_by` - Filter by creator
- `search` - Search by subject or number

**Create Ticket:** `POST /api/tenant/tickets`
**Get Ticket:** `GET /api/tenant/tickets/{id}`
**Update Ticket:** `PUT /api/tenant/tickets/{id}`
**Delete Ticket:** `DELETE /api/tenant/tickets/{id}`
**Assign Ticket:** `POST /api/tenant/tickets/{id}/assign`
**Update Status:** `POST /api/tenant/tickets/{id}/status`
**Update Priority:** `POST /api/tenant/tickets/{id}/priority`
**Resolve Ticket:** `POST /api/tenant/tickets/{id}/resolve`
**Close Ticket:** `POST /api/tenant/tickets/{id}/close`
**Reopen Ticket:** `POST /api/tenant/tickets/{id}/reopen`

### Ticket Comments

**List Comments:** `GET /api/tenant/tickets/{id}/comments`
**Create Comment:** `POST /api/tenant/tickets/{id}/comments`
**Get Comment:** `GET /api/tenant/tickets/comments/{commentId}`
**Update Comment:** `PUT /api/tenant/tickets/comments/{commentId}`
**Delete Comment:** `DELETE /api/tenant/tickets/comments/{commentId}`

### Dashboard

**Get Ticket Stats:** `GET /api/tenant/tickets/stats`
**Get My Tickets:** `GET /api/tenant/tickets/my-tickets`
**Get Assigned Tickets:** `GET /api/tenant/tickets/assigned`

## Services

### TicketService
- Ticket CRUD operations
- Ticket assignment logic
- Status management
- Priority management
- Ticket lifecycle

### TicketCommentService
- Comment CRUD operations
- Comment notification
- Internal comment handling

## Repositories

### TicketRepository
- Ticket data access
- Ticket filtering and searching
- Status-based queries
- Assignment-based queries

### TicketCommentRepository
- Comment data access
- Comment filtering and searching
- Ticket-based queries
- Internal comment queries

## DTOs

### CreateTicketData
Typed input transfer object for ticket creation with validation.

### UpdateTicketData
Typed input transfer object for ticket updates with validation.

### CreateCommentData
Typed input transfer object for comment creation with validation.

## Configuration

### Module Configuration

Module configuration in `Config/ticket.php`:

```php
return [
    'tickets' => {
        'auto_assign' => false,
        'auto_number' => true,
        'number_prefix' => 'TKT-',
        'number_length' => 6,
    },
    'notifications' => {
        'on_create' => true,
        'on_assign' => true,
        'on_comment' => true,
        'on_status_change' => true,
    },
    'priorities' => [
        'default' => 'medium',
        'auto_escalation_days' => 7,
    ],
];
```

## Ticket Status

- `open` - Open ticket
- `in_progress` - In progress
- `pending` - Pending response
- `resolved` - Resolved
- `closed` - Closed

## Priority Levels

- `low` - Low priority
- `medium` - Medium priority
- `high` - High priority
- `urgent` - Urgent priority

## Ticket Categories

- `support` - General support
- `bug` - Bug report
- `feature` - Feature request
- `billing` - Billing issue
- `technical` - Technical issue
- `other` - Other

## Notifications

The module sends notifications for:
- Ticket creation
- Ticket assignment
- New comments
- Status changes
- Resolution
- Closure

## Business Rules

- Ticket numbers are auto-generated
- Closed tickets can be reopened
- Resolved tickets can be closed
- Internal comments are only visible to staff
- Ticket status transitions are controlled
- Priority escalation is automatic (if configured)

## Permissions

Ticket module permissions follow the pattern: `ticket.{resource}.{action}`

- `ticket.tickets.view` - View tickets
- `ticket.tickets.create` - Create tickets
- `ticket.tickets.edit` - Edit tickets
- `ticket.tickets.delete` - Delete tickets
- `ticket.tickets.assign` - Assign tickets
- `ticket.tickets.resolve` - Resolve tickets
- `ticket.tickets.close` - Close tickets
- `ticket.comments.view` - View comments
- `ticket.comments.create` - Create comments
- `ticket.comments.edit` - Edit comments
- `ticket.comments.delete` - Delete comments
- `ticket.comments.internal` - Create internal comments

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Ticket/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Notification tests
- Status transition tests

## Related Documentation

- [Ticket Workflow Guide](../../backend/documentation/ticket/workflow.md)
- [Support Best Practices](../../backend/documentation/ticket/best-practices.md)
