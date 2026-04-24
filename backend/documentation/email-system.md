# Dynamic Email System Documentation

## Overview

The SaaS Dashboard now features a dynamic email template system that allows administrators to manage email templates through the database instead of hardcoded templates. This system provides flexibility, maintainability, and easy customization of email communications.

## Features

- **Dynamic Template Management**: Create, update, and manage email templates through the database
- **Variable Substitution**: Support for dynamic variables in email templates
- **Fallback System**: Automatic fallback to hardcoded templates if dynamic templates are not found
- **Command Line Interface**: Manage templates via Artisan commands
- **Web Interface**: Manage templates through the admin panel
- **Migration Support**: Easy migration from hardcoded to dynamic templates

## Architecture

### Core Components

1. **EmailTemplate Entity** (`modules/Email/Entities/EmailTemplate.php`)
   - Database model for storing email templates
   - Fields: `name`, `description`, `subject`, `body`, `status`

2. **DynamicEmailService** (`modules/Email/Services/DynamicEmailService.php`)
   - Service for sending emails using dynamic templates
   - Handles variable substitution and template processing

3. **EmailTemplateCommand** (`app/Console/Commands/EmailTemplateCommand.php`)
   - Artisan command for managing email templates
   - Supports CRUD operations and seeding

4. **Updated Mail Classes**
   - `RegistrationMail` and `ForgetPasswordMail` now use dynamic templates
   - Automatic fallback to hardcoded templates if dynamic templates are not available

## Installation & Setup

### 1. Run Migrations

```bash
# Add subject column to existing email_templates table
php artisan migrate

# Run the email template seeder
php artisan email:templates seed
```

### 2. Verify Installation

```bash
# List all email templates
php artisan email:templates list
```

## Usage

### Command Line Interface

#### List Templates
```bash
php artisan email:templates list
```

#### Create Template
```bash
php artisan email:templates create --name="Welcome Email" --subject="Welcome!" --description="Welcome new users" --body="<h1>Welcome {{name}}!</h1>"
```

#### Update Template
```bash
php artisan email:templates update --id=1 --subject="Updated Welcome Subject"
```

#### Delete Template
```bash
php artisan email:templates delete --id=1
```

#### Seed Default Templates
```bash
php artisan email:templates seed
```

### Web Interface

Access the email template management through the admin panel:
- Navigate to **Email Templates** section
- Create, edit, and manage templates through the web interface
- Preview templates before saving

### Programmatic Usage

#### Using DynamicEmailService

```php
use Modules\Email\Services\DynamicEmailService;

$dynamicEmailService = app(DynamicEmailService::class);

// Send email using dynamic template
$success = $dynamicEmailService->sendTemplateEmail(
    'User Registration',
    'user@example.com',
    [
        'name' => 'John Doe',
        'verification_url' => 'https://example.com/verify?token=abc123'
    ]
);
```

#### Using Updated Mail Classes

The existing mail classes (`RegistrationMail`, `ForgetPasswordMail`) automatically use dynamic templates:

```php
use Modules\Auth\Mail\RegistrationMail;

$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'token' => 'abc123'
];

Mail::to('john@example.com')->send(new RegistrationMail($data));
```

## Template Variables

### Available Variables

The system supports the following variables in email templates:

#### Default Variables (automatically available)
- `{{app_name}}` - Application name from config
- `{{app_url}}` - Application URL from config

#### Registration Template Variables
- `{{name}}` - User's name
- `{{email}}` - User's email
- `{{token}}` - Verification token
- `{{verification_url}}` - Complete verification URL

#### Password Reset Template Variables
- `{{name}}` - User's name
- `{{email}}` - User's email
- `{{token}}` - Reset token
- `{{reset_url}}` - Complete reset URL

### Custom Variables

You can add custom variables when sending emails:

```php
$dynamicEmailService->sendTemplateEmail(
    'Custom Template',
    'user@example.com',
    [
        'custom_variable' => 'Custom Value',
        'another_variable' => 'Another Value'
    ]
);
```

## Template Examples

### Registration Email Template

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">Registration Confirmation</h1>
        <p>Dear {{name}},</p>
        <p>Thank you for registration. To complete the registration process, please click the "Confirm Email" button below.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{verification_url}}" style="background-color: #3490dc; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Confirm Email</a>
        </div>
        <p>If you are having trouble clicking the "Confirm Email" button, you can also copy and paste the following link into your browser:</p>
        <p style="word-break: break-all; color: #666;">{{verification_url}}</p>
        <p>If you did not initiate this registration, please ignore this email.</p>
        <p>Thanks,<br>{{app_name}} Team.</p>
    </div>
</body>
</html>
```

### Password Reset Template

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #e74c3c;">Password Reset Request</h1>
        <p>Dear {{name}},</p>
        <p>We received a request to reset your password. If you did not make this request, please ignore this email.</p>
        <p>To reset your password, click the button below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{reset_url}}" style="background-color: #3490dc; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a>
        </div>
        <p>If you are having trouble clicking the "Reset Password" button, you can also copy and paste the following link into your browser:</p>
        <p style="word-break: break-all; color: #666;">{{reset_url}}</p>
        <p>If you have any questions or need assistance, please contact our support team.</p>
        <p>Thanks,<br>{{app_name}} Team.</p>
    </div>
</body>
</html>
```

## Migration from Hardcoded Templates

### Automatic Migration

The system automatically migrates from hardcoded templates to dynamic templates:

1. **RegistrationMail**: Uses "User Registration" template
2. **ForgetPasswordMail**: Uses "Password Reset Request" template

### Fallback System

If a dynamic template is not found, the system automatically falls back to the original hardcoded template, ensuring backward compatibility.

### Manual Migration

To migrate additional templates:

1. Create the template in the database using the web interface or command line
2. Update the corresponding mail class to use the dynamic template
3. Test the email functionality
4. Remove the hardcoded template if desired

## Troubleshooting

### Common Issues

#### Template Not Found
- **Error**: "Email template 'Template Name' not found"
- **Solution**: Ensure the template exists in the database and has status 'active'

#### Variable Not Substituted
- **Error**: Variables like `{{name}}` appear literally in emails
- **Solution**: Check that variables are passed correctly to the email sending function

#### Migration Issues
- **Error**: Database migration fails
- **Solution**: Check database permissions and ensure the email_templates table exists

### Debugging

#### Enable Logging
```php
// In config/logging.php, ensure email logs are enabled
'channels' => [
    'email' => [
        'driver' => 'single',
        'path' => storage_path('logs/email.log'),
        'level' => 'debug',
    ],
],
```

#### Check Template Status
```bash
# List all templates to verify status
php artisan email:templates list
```

#### Test Email Sending
```php
// Test email sending in tinker
php artisan tinker

// Send test email
$dynamicEmailService = app(\Modules\Email\Services\DynamicEmailService::class);
$dynamicEmailService->sendTemplateEmail('User Registration', 'test@example.com', ['name' => 'Test User']);
```

## Best Practices

### Template Design
1. **Responsive Design**: Use responsive HTML/CSS for email templates
2. **Fallback Content**: Include fallback content for email clients that don't support HTML
3. **Testing**: Test templates across different email clients
4. **Accessibility**: Ensure templates are accessible to users with disabilities

### Variable Management
1. **Consistent Naming**: Use consistent variable naming conventions
2. **Documentation**: Document all available variables for each template
3. **Validation**: Validate variables before sending emails
4. **Default Values**: Provide default values for optional variables

### Performance
1. **Caching**: Consider caching frequently used templates
2. **Database Optimization**: Index the email_templates table for better performance
3. **Queue Processing**: Use queue processing for bulk email sending

## Security Considerations

### Template Security
1. **HTML Sanitization**: Sanitize HTML content in templates
2. **Variable Validation**: Validate and sanitize all template variables
3. **Access Control**: Restrict template management to authorized users only

### Email Security
1. **SPF/DKIM**: Configure SPF and DKIM records for email authentication
2. **Rate Limiting**: Implement rate limiting for email sending
3. **Content Filtering**: Filter potentially malicious content in templates

## API Reference

### DynamicEmailService Methods

#### `sendTemplateEmail(string $templateName, string $to, array $variables = [], array $options = []): bool`
Send an email using a dynamic template.

**Parameters:**
- `$templateName`: Name of the template to use
- `$to`: Recipient email address
- `$variables`: Array of variables to substitute in the template
- `$options`: Additional options (email_credential_id, additional_data)

**Returns:** `bool` - Success status

#### `getTemplateByName(string $name): ?EmailTemplate`
Get a template by its name.

**Parameters:**
- `$name`: Template name

**Returns:** `EmailTemplate|null` - Template object or null if not found

#### `getAllTemplates(): Collection`
Get all active email templates.

**Returns:** `Collection` - Collection of EmailTemplate objects

#### `templateExists(string $name): bool`
Check if a template exists.

**Parameters:**
- `$name`: Template name

**Returns:** `bool` - True if template exists and is active

## Changelog

### Version 1.0.0
- Initial implementation of dynamic email template system
- Migration from hardcoded templates to database-driven templates
- Command line interface for template management
- Web interface for template management
- Automatic fallback system for backward compatibility
- Support for variable substitution in templates
- Updated RegistrationMail and ForgetPasswordMail classes

## Support

For issues, questions, or feature requests related to the email system:

1. Check this documentation first
2. Review the troubleshooting section
3. Check the application logs
4. Contact the development team

## Contributing

When contributing to the email system:

1. Follow the existing code style and patterns
2. Add appropriate tests for new functionality
3. Update this documentation for any changes
4. Ensure backward compatibility when possible
5. Test across different email clients and devices
