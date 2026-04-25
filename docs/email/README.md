# Email Module Documentation

## Overview

The Email module provides comprehensive email management functionality including email templates, email campaigns, email tracking, and transactional email management. It enables sending, tracking, and managing emails for various purposes such as marketing, notifications, and system communications.

## Architecture

### Module Structure

```
Email/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # Email entities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Email Templates
- `id` - Primary key
- `name` - Template name
- `subject` - Email subject
- `content` - Email content (HTML)
- `text_content` - Plain text content
- `variables` - Template variables (JSON)
- `category` - Template category
- `status` - Template status
- `created_at`, `updated_at` - Timestamps

#### Email Campaigns
- `id` - Primary key
- `name` - Campaign name
- `subject` - Campaign subject
- `template_id` - Associated template
- `from_email` - From email address
- `from_name` - From name
- `status` - Campaign status (draft, scheduled, sending, sent, failed)
- `scheduled_at` - Scheduled send time
- `sent_at` - Actual send time
- `created_at`, `updated_at` - Timestamps

#### Email Logs
- `id` - Primary key
- `campaign_id` - Associated campaign (nullable)
- `template_id` - Associated template (nullable)
- `to_email` - Recipient email
- `to_name` - Recipient name
- `subject` - Email subject
- `status` - Email status (pending, sent, delivered, opened, clicked, bounced, failed)
- `sent_at` - Sent timestamp
- `delivered_at` - Delivered timestamp
- `opened_at` - Opened timestamp
- `clicked_at` - Clicked timestamp
- `error_message` - Error message (if failed)
- `created_at`, `updated_at` - Timestamps

#### Email Attachments
- `id` - Primary key
- `email_log_id` - Associated email log
- `file_name` - File name
- `file_path` - File path
- `file_size` - File size
- `mime_type` - MIME type
- `created_at` - Timestamp

#### Email Lists
- `id` - Primary key
- `name` - List name
- `description` - List description
- `status` - List status
- `created_at`, `updated_at` - Timestamps

#### Email Subscribers
- `id` - Primary key
- `email_list_id` - Associated email list
- `email` - Subscriber email
- `name` - Subscriber name
- `status` - Subscription status (active, unsubscribed, bounced)
- `subscribed_at` - Subscription timestamp
- `unsubscribed_at` - Unsubscription timestamp
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Email Templates

**List Templates:** `GET /api/tenant/email/templates`
**Create Template:** `POST /api/tenant/email/templates`
**Get Template:** `GET /api/tenant/email/templates/{id}`
**Update Template:** `PUT /api/tenant/email/templates/{id}`
**Delete Template:** `DELETE /api/tenant/email/templates/{id}`
**Preview Template:** `POST /api/tenant/email/templates/{id}/preview`

### Email Campaigns

**List Campaigns:** `GET /api/tenant/email/campaigns`
**Create Campaign:** `POST /api/tenant/email/campaigns`
**Get Campaign:** `GET /api/tenant/email/campaigns/{id}`
**Update Campaign:** `PUT /api/tenant/email/campaigns/{id}`
**Delete Campaign:** `DELETE /api/tenant/email/campaigns/{id}`
**Send Campaign:** `POST /api/tenant/email/campaigns/{id}/send`
**Schedule Campaign:** `POST /api/tenant/email/campaigns/{id}/schedule`
**Get Campaign Stats:** `GET /api/tenant/email/campaigns/{id}/stats`

### Email Logs

**List Email Logs:** `GET /api/tenant/email/logs`

**Query Parameters:**
- `campaign_id` - Filter by campaign
- `template_id` - Filter by template
- `status` - Filter by status
- `date_from` - Filter by date from
- `date_to` - Filter by date to

**Get Email Log:** `GET /api/tenant/email/logs/{id}`
**Resend Email:** `POST /api/tenant/email/logs/{id}/resend`

### Email Lists

**List Email Lists:** `GET /api/tenant/email/lists`
**Create Email List:** `POST /api/tenant/email/lists`
**Get Email List:** `GET /api/tenant/email/lists/{id}`
**Update Email List:** `PUT /api/tenant/email/lists/{id}`
**Delete Email List:** `DELETE /api/tenant/email/lists/{id}`
**Get List Subscribers:** `GET /api/tenant/email/lists/{id}/subscribers`

### Email Subscribers

**List Subscribers:** `GET /api/tenant/email/subscribers`
**Create Subscriber:** `POST /api/tenant/email/subscribers`
**Get Subscriber:** `GET /api/tenant/email/subscribers/{id}`
**Update Subscriber:** `PUT /api/tenant/email/subscribers/{id}`
**Delete Subscriber:** `DELETE /api/tenant/email/subscribers/{id}`
**Unsubscribe Subscriber:** `POST /api/tenant/email/subscribers/{id}/unsubscribe`
**Resubscribe Subscriber:** `POST /api/tenant/email/subscribers/{id}/resubscribe`

## Services

### EmailTemplateService
- Template CRUD operations
- Template variable parsing
- Template preview generation
- Template validation

### EmailCampaignService
- Campaign CRUD operations
- Campaign scheduling
- Campaign sending logic
- Campaign statistics calculation

### EmailLogService
- Email logging
- Email status tracking
- Delivery tracking
- Open/click tracking

### EmailListService
- List CRUD operations
- Subscriber management
- List segmentation
- Import/export functionality

## Repositories

### EmailTemplateRepository
- Template data access
- Template filtering and searching
- Category-based queries

### EmailCampaignRepository
- Campaign data access
- Campaign filtering and searching
- Status-based queries

### EmailLogRepository
- Log data access
- Log filtering and searching
- Analytics queries

### EmailListRepository
- List data access
- List filtering and searching
- Subscriber count queries

### EmailSubscriberRepository
- Subscriber data access
- Subscriber filtering and searching
- Subscription status queries

## DTOs

### CreateTemplateData
Typed input transfer object for template creation with validation.

### UpdateTemplateData
Typed input transfer object for template updates with validation.

### CreateCampaignData
Typed input transfer object for campaign creation with validation.

### CreateSubscriberData
Typed input transfer object for subscriber creation with validation.

## Configuration

### Environment Variables

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="SaaS Platform"
```

### Module Configuration

Module configuration in `Config/email.php`:

```php
return [
    'templates' => [
        'max_content_size' => 1024, // KB
        'allowed_categories' => ['marketing', 'transactional', 'notification'],
    ],
    'campaigns' => [
        'max_recipients_per_campaign' => 10000,
        'batch_size' => 100,
        'rate_limit_per_minute' => 60,
    ],
    'tracking' => [
        'enabled' => true,
        'open_tracking' => true,
        'click_tracking' => true,
    ],
    'lists' => [
        'max_subscribers_per_list' => 50000,
        'allow_import' => true,
    ],
];
```

## Email Status

- `pending` - Waiting to be sent
- `sent` - Successfully sent
- `delivered` - Delivered to recipient
- `opened` - Opened by recipient
- `clicked` - Link clicked
- `bounced` - Bounced back
- `failed` - Failed to send

## Template Variables

Templates support dynamic variables using the syntax `{{variable_name}}`. Common variables include:

- `{{user_name}}` - Recipient name
- `{{user_email}}` - Recipient email
- `{{company_name}}` - Company name
- `{{unsubscribe_url}}` - Unsubscribe link
- `{{tracking_pixel}}` - Tracking pixel for open tracking

## Permissions

Email module permissions follow the pattern: `email.{resource}.{action}`

- `email.templates.view` - View templates
- `email.templates.create` - Create templates
- `email.templates.edit` - Edit templates
- `email.templates.delete` - Delete templates
- `email.campaigns.view` - View campaigns
- `email.campaigns.create` - Create campaigns
- `email.campaigns.edit` - Edit campaigns
- `email.campaigns.delete` - Delete campaigns
- `email.campaigns.send` - Send campaigns
- `email.logs.view` - View email logs
- `email.logs.resend` - Resend emails
- `email.lists.view` - View email lists
- `email.lists.create` - Create email lists
- `email.lists.edit` - Edit email lists
- `email.lists.delete` - Delete email lists
- `email.subscribers.view` - View subscribers
- `email.subscribers.create` - Create subscribers
- `email.subscribers.edit` - Edit subscribers
- `email.subscribers.delete` - Delete subscribers
- `email.subscribers.unsubscribe` - Unsubscribe subscribers

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Email/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Template parsing tests
- Campaign sending tests

## Related Documentation

- [Email Configuration Guide](../../backend/documentation/email/configuration.md)
- [Email Best Practices](../../backend/documentation/email/best-practices.md)
