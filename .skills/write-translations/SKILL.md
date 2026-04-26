# Write Translations Skill

## Overview
This skill documents the process for adding and managing translations in the SaaS dashboard backend. All user-facing messages must use the `translate()` helper instead of hardcoded strings.

## Translation Key Convention

### Format
```
{context}.{key}
```

### Common Contexts
- `message` - General user messages (success, error, validation)
- `auth` - Authentication-related messages
- `exception` - Exception/error messages
- `common` - Common UI elements (buttons, labels)
- `nav` - Navigation items
- `form` - Form labels
- `status` - Status values
- `{module}` - Module-specific translations (e.g., `crm.lead_created`)

### Key Naming Patterns
- **Success messages**: `message.{action}_successfully` (e.g., `message.created_successfully`)
- **Error messages**: `message.{action}_failed` or `message.operation_failed`
- **Validation errors**: `message.validation_failed`
- **Resource not found**: `message.resource_not_found`
- **Auth errors**: `auth.unauthorized`, `auth.unauthenticated`
- **Domain exceptions**: `exception.{rule_violation}` (e.g., `exception.cannot_transition_status`)

## Adding New Translations

### Step 1: Add to JSON Translation File
Create or update the module's JSON translation file at:
```
modules/{Module}/resources/lang/{locale}.json
```

Example (`modules/CRM/resources/lang/en.json`):
```json
{
  "crm.lead": "Lead",
  "crm.lead_created": "Lead created successfully",
  "crm.lead_updated": "Lead updated successfully",
  "crm.lead_deleted": "Lead deleted successfully",
  "crm.lead_not_found": "Lead not found"
}
```

### Step 2: Add All Locales
Add translations for all supported locales (en, ar, de):
- `en.json` - English
- `ar.json` - Arabic
- `de.json` - German

### Step 3: Use in Code
Replace hardcoded messages with `translate()` calls:

```php
// Before
return $this->apiSuccess($lead, 'Lead created successfully', 201);

// After
return $this->apiSuccess($lead, translate('crm.lead_created'), 201);
```

### Step 4: Add to TranslationSeeder (Optional)
For core/common translations, add to `modules/Localization/database/seeders/TranslationSeeder.php`:

```php
[
    'translation_key' => 'message.created_successfully',
    'translations' => [
        'en' => 'Created successfully',
        'ar' => 'تم الإنشاء بنجاح',
        'de' => 'Erfolgreich erstellt',
    ],
    'translation_context' => 'messages',
    'is_shareable' => true,
],
```

## Attribute Substitution

For dynamic messages, use attribute placeholders with `:attribute` syntax:

```php
// In JSON
{
  "exception.cannot_transition_status": "Cannot transition status from :from to :to"
}

// In code
throw new \RuntimeException(translate('exception.cannot_transition_status', [
    'from' => $this->status,
    'to' => $to->value
]));
```

## Common Translation Keys

### Messages
- `message.created_successfully` - Generic success message for creation
- `message.updated_successfully` - Generic success message for updates
- `message.deleted_successfully` - Generic success message for deletion
- `message.operation_failed` - Generic operation failure
- `message.validation_failed` - Validation error
- `message.resource_not_found` - Resource not found
- `message.action_completed` - Generic action completed

### Authentication
- `auth.unauthorized` - User lacks permission
- `auth.unauthenticated` - User not logged in

### Exceptions
- `exception.cannot_delete_with_associated` - Cannot delete with related records
- `exception.money_negative` - Money amount cannot be negative
- `exception.failed_store_file` - File storage failed

## TranslationSeeder Behavior

The `TranslationSeeder` in the Localization module:
1. **First** loads translations from per-module JSON files (`modules/{Module}/resources/lang/{locale}.json`)
2. **Then** merges with hardcoded translations in the seeder
3. Hardcoded translations take precedence for conflicts
4. All translations are upserted into the database

## Best Practices

1. **Always use `translate()`** for user-facing messages
2. **Use generic keys** when possible (`message.created_successfully` vs `crm.lead_created`)
3. **Module-specific keys** only when the message is unique to that module
4. **Attribute substitution** for dynamic values (status transitions, names, etc.)
5. **Keep JSON files organized** by context and logical grouping
6. **Run seeder** after adding new translations to JSON files

## Running the Seeder

```bash
php artisan db:seed --class=Modules\\Localization\\Database\\Seeders\\TranslationSeeder
```

## File Locations

- **Module JSON files**: `modules/{Module}/resources/lang/{locale}.json`
- **Common JSON files**: `modules/Localization/resources/lang/{locale}.json`
- **Seeder**: `modules/Localization/database/seeders/TranslationSeeder.php`
- **Helper**: `app/Helpers/TranslateHelper.php`
