<?php

namespace Modules\Utilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Utilities\Entities\Release;
use Carbon\Carbon;

class ReleaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $releases = [
            [
                'object_model' => 'Modules\\Utilities\\Entities\\Module',
                'object_id' => 1,
                'name' => 'Authentication Module v2.1.0',
                'slug' => 'auth-module-v2-1-0',
                'description' => 'Major update to the authentication system with enhanced security features',
                'body' => 'This release includes significant improvements to the authentication system:

## New Features
- Two-factor authentication (2FA) support
- Social login integration (Google, Facebook, LinkedIn)
- Password strength requirements
- Account lockout protection
- Session management improvements

## Security Enhancements
- Enhanced password hashing
- Rate limiting for login attempts
- IP-based access controls
- Audit logging for security events

## Bug Fixes
- Fixed session timeout issues
- Resolved password reset email problems
- Corrected user role assignment bugs

## Performance Improvements
- Optimized database queries
- Reduced memory usage
- Faster login processing

## Breaking Changes
- Updated password requirements (minimum 8 characters, must include uppercase, lowercase, number, and special character)
- Session timeout reduced from 24 hours to 8 hours for security

Please update your applications accordingly.',
                'version' => '2.1.0',
                'status' => 'active',
                'release_date' => Carbon::now()->subDays(5),
            ],
            [
                'object_model' => 'Modules\\Utilities\\Entities\\Module',
                'object_id' => 2,
                'name' => 'Customer Management v1.5.2',
                'slug' => 'customer-management-v1-5-2',
                'description' => 'Minor update with bug fixes and performance improvements',
                'body' => 'This release focuses on stability and performance improvements:

## Bug Fixes
- Fixed customer search functionality
- Resolved data export issues
- Corrected customer profile image upload problems
- Fixed duplicate customer creation bug

## Performance Improvements
- Optimized customer list loading
- Improved search response times
- Reduced memory usage for large customer datasets

## UI/UX Improvements
- Better mobile responsiveness
- Improved form validation messages
- Enhanced customer profile layout

## New Features
- Bulk customer import from CSV
- Advanced filtering options
- Customer activity timeline

This is a maintenance release with no breaking changes.',
                'version' => '1.5.2',
                'status' => 'active',
                'release_date' => Carbon::now()->subDays(3),
            ],
            [
                'object_model' => 'Modules\\Utilities\\Entities\\Module',
                'object_id' => 3,
                'name' => 'Development Tools v1.0.0',
                'slug' => 'development-tools-v1-0-0',
                'description' => 'Initial release of the development tools module',
                'body' => 'Welcome to the first release of the Development Tools module!

## Features
- Code generator for common patterns
- Database migration tools
- API testing interface
- Log viewer and analyzer
- Performance monitoring dashboard
- Debug utilities

## Getting Started
1. Access the development tools from the admin panel
2. Use the code generator to create new modules, controllers, and models
3. Monitor your application performance with the built-in tools
4. Test your APIs using the integrated testing interface

## Requirements
- PHP 8.1 or higher
- Laravel 10.x
- MySQL 8.0 or PostgreSQL 13+

## Support
For questions and support, please refer to the documentation or contact the development team.',
                'version' => '1.0.0',
                'status' => 'active',
                'release_date' => Carbon::now()->subDays(1),
            ],
            [
                'object_model' => 'Modules\\Utilities\\Entities\\Module',
                'object_id' => 4,
                'name' => 'Email Management v2.0.0',
                'slug' => 'email-management-v2-0-0',
                'description' => 'Major update with new email templates and delivery improvements',
                'body' => 'This is a major update to the Email Management system:

## New Features
- Drag-and-drop email template builder
- A/B testing for email campaigns
- Advanced email scheduling
- Email analytics and tracking
- Template versioning system
- Multi-language email support

## Improvements
- Better email delivery rates
- Improved spam score optimization
- Enhanced template rendering engine
- Better mobile email compatibility

## New Integrations
- Mailchimp integration
- SendGrid API support
- Amazon SES configuration
- SMTP provider management

## Breaking Changes
- Updated template syntax (migration guide available)
- Changed API endpoints for email sending
- Updated configuration file structure

Please review the migration guide before updating.',
                'version' => '2.0.0',
                'status' => 'active',
                'release_date' => Carbon::now()->subDays(7),
            ],
            [
                'object_model' => 'Modules\\Utilities\\Entities\\Module',
                'object_id' => 5,
                'name' => 'File Manager v1.3.1',
                'slug' => 'file-manager-v1-3-1',
                'description' => 'Security update and bug fixes',
                'body' => 'This release addresses security vulnerabilities and includes important bug fixes:

## Security Updates
- Fixed file upload vulnerability
- Enhanced file type validation
- Improved access control mechanisms
- Added virus scanning integration

## Bug Fixes
- Fixed file download issues for large files
- Resolved thumbnail generation problems
- Corrected file permission handling
- Fixed storage quota calculation errors

## New Features
- File versioning support
- Advanced search functionality
- Bulk file operations
- File sharing with expiration dates

## Performance Improvements
- Optimized file upload process
- Improved file listing performance
- Reduced memory usage for large files

This is a critical security update. Please update immediately.',
                'version' => '1.3.1',
                'status' => 'active',
                'release_date' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($releases as $releaseData) {
            Release::firstOrCreate(
                ['version' => $releaseData['version']], 
                $releaseData
            );
        }

        $this->command->info('Releases seeded successfully!');
    }
}
