# Seeding Commands Documentation

## Overview

This document describes the two main seeding commands created for the SaaS Dashboard project to streamline data seeding for both development and production environments.

## Commands

### 1. Real Data Seeding Command

**Command:** `php artisan seed:real-data`

**Purpose:** Seeds essential system data required for the application to function properly, including languages, email templates, configurations, roles, and permissions.

**Usage:**
```bash
# Seed all real data
php artisan seed:real-data

# Force seeding even if data exists
php artisan seed:real-data --force

# Seed specific modules only
php artisan seed:real-data --modules=Localization,Email

# Seed specific modules with force
php artisan seed:real-data --modules=Auth,Utilities --force
```

**Available Modules:**
- **Localization**: Languages and translations
- **Email**: Email templates and credentials
- **Development**: System configurations and settings
- **Utilities**: System modules and utilities
- **Auth**: Roles, permissions, and default users
- **Tenant**: Landlord tenant setup

**What it seeds:**
- Languages (English, Arabic, German)
- Email templates (Registration, Password Reset, etc.)
- Email credentials (SMTP configurations)
- System configurations
- System modules
- Roles and permissions
- Default landlord user
- Landlord tenant setup

### 2. Dummy Data Seeding Command

**Command:** `php artisan seed:dummy-data`

**Purpose:** Seeds test data for development and testing purposes, including sample users, categories, types, industries, and other dummy content.

**Usage:**
```bash
# Seed all dummy data
php artisan seed:dummy-data

# Force seeding even if data exists
php artisan seed:dummy-data --force

# Seed specific modules only
php artisan seed:dummy-data --modules=Auth,Utilities

# Seed specific modules with force
php artisan seed:dummy-data --modules=Email,Geography --force
```

**Available Modules:**
- **Auth**: Dummy users for testing
- **Utilities**: Sample categories, types, industries, and tags
- **Email**: Dummy email campaigns
- **Geography**: Sample countries, provinces, and cities
- **Tenant**: Dummy tenant data

**What it seeds:**
- Sample users for testing
- Categories (Technology, Business, Education, etc.)
- Types (Software, Service, Product, etc.)
- Industries (Technology, Healthcare, Finance, etc.)
- Tags for categorization
- Dummy email campaigns
- Sample geographical data
- Test tenant data

## Implementation Details

### Command Structure

Both commands follow a similar structure:

1. **Validation**: Check for existing data and prompt user if needed
2. **Module Processing**: Process each specified module
3. **Seeder Execution**: Run individual seeders for each module
4. **Error Handling**: Catch and report errors gracefully
5. **Progress Reporting**: Show detailed progress and results

### Safety Features

- **Duplicate Prevention**: Commands check for existing data before seeding
- **Confirmation Prompts**: Ask for confirmation when existing data is found
- **Force Option**: Override safety checks when needed
- **Error Handling**: Graceful error handling with detailed messages
- **Module Filtering**: Seed only specific modules when needed

### Seeder Organization

Seeders are organized by module and purpose:

```
modules/
├── Auth/
│   └── Database/Seeders/
│       ├── LandlordUserSeeder.php (real data)
│       └── DummyUserSeeder.php (dummy data)
├── Email/
│   └── Database/Seeders/
│       ├── EmailTemplateSeeder.php (real data)
│       ├── EmailCredentialSeeder.php (real data)
│       └── DummyEmailCampaignSeeder.php (dummy data)
├── Utilities/
│   └── Database/Seeders/
│       ├── ModulesSeeder.php (real data)
│       ├── DummyCategorySeeder.php (dummy data)
│       ├── DummyTypeSeeder.php (dummy data)
│       ├── DummyIndustrySeeder.php (dummy data)
│       └── DummyTagSeeder.php (dummy data)
└── ...
```

## Usage Examples

### Development Setup

For a new development environment:

```bash
# 1. First, seed real data (essential system data)
php artisan seed:real-data

# 2. Then, seed dummy data for testing
php artisan seed:dummy-data
```

### Production Setup

For production environment:

```bash
# Only seed real data (no dummy data in production)
php artisan seed:real-data --force
```

### Testing Specific Features

To test specific functionality:

```bash
# Test email functionality
php artisan seed:real-data --modules=Email
php artisan seed:dummy-data --modules=Email

# Test user management
php artisan seed:real-data --modules=Auth
php artisan seed:dummy-data --modules=Auth
```

### Module-Specific Seeding

To seed only specific modules:

```bash
# Seed only localization data
php artisan seed:real-data --modules=Localization

# Seed only utilities dummy data
php artisan seed:dummy-data --modules=Utilities
```

## Error Handling

### Common Issues

1. **Database Connection Issues**
   - Ensure database is properly configured
   - Check landlord database connection

2. **Missing Seeders**
   - Commands will warn about missing seeders
   - Check seeder class names and namespaces

3. **Duplicate Data**
   - Use `--force` flag to override duplicate checks
   - Or manually clean database before seeding

4. **Permission Issues**
   - Ensure proper file permissions
   - Check database user permissions

### Troubleshooting

```bash
# Check if commands are registered
php artisan list | grep seed

# Run with verbose output
php artisan seed:real-data -v

# Check specific seeder
php artisan db:seed --class="Modules\Email\Database\Seeders\EmailTemplateSeeder" --database=landlord
```

## Best Practices

### Development

1. **Always seed real data first** - Essential system data must be present
2. **Use dummy data for testing** - Don't use real data for development testing
3. **Seed incrementally** - Use module-specific seeding for targeted testing
4. **Clean between tests** - Reset database between test runs

### Production

1. **Only use real data** - Never seed dummy data in production
2. **Use force flag carefully** - Only when you're sure about the operation
3. **Backup before seeding** - Always backup production database
4. **Test in staging first** - Test seeding commands in staging environment

### Maintenance

1. **Update seeders regularly** - Keep seeders in sync with model changes
2. **Document new seeders** - Add new seeders to command configurations
3. **Version control seeders** - Keep seeders in version control
4. **Monitor seeding results** - Check logs and output for errors

## Integration with Existing Workflow

### CI/CD Pipeline

```yaml
# Example GitHub Actions workflow
- name: Seed Real Data
  run: php artisan seed:real-data --force

- name: Run Tests
  run: php artisan seed:dummy-data && php artisan test
```

### Docker Setup

```dockerfile
# Example Dockerfile
RUN php artisan seed:real-data --force
```

### Local Development

```bash
# Quick setup script
#!/bin/bash
php artisan migrate
php artisan seed:real-data
php artisan seed:dummy-data
echo "Development environment ready!"
```

## Future Enhancements

### Planned Features

1. **Environment Detection** - Automatically detect environment and seed accordingly
2. **Seeder Dependencies** - Handle seeder dependencies automatically
3. **Progress Bars** - Add progress bars for long-running operations
4. **Rollback Support** - Add ability to rollback seeded data
5. **Configuration Files** - External configuration files for seeder settings

### Contributing

When adding new seeders:

1. **Follow naming convention** - Use descriptive names with `Dummy` or real data purpose
2. **Add to command configuration** - Update command arrays with new seeders
3. **Test thoroughly** - Test seeders in isolation and with commands
4. **Document changes** - Update this documentation with new seeders
5. **Handle errors gracefully** - Add proper error handling and validation

## Changelog

### Version 1.0.0
- Initial implementation of seeding commands
- Support for real and dummy data seeding
- Module-specific seeding capabilities
- Force option for overriding safety checks
- Comprehensive error handling and reporting
- Integration with existing seeder infrastructure

## Support

For issues or questions related to the seeding commands:

1. Check this documentation first
2. Review command output and error messages
3. Check database connections and permissions
4. Verify seeder class names and namespaces
5. Contact the development team for assistance

## Related Documentation

- [Email System Documentation](email-system.md)
- [Complete Module Seeders](complete-module-seeders.md)
- [Utilities Module Seeders](utilities-module-seeders.md)
- [Main README](../README.md)
