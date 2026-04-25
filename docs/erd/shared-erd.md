# Shared Database ERD

This document contains the shared database schema for the SaaS Dashboard application.

---

## Core Tables

#### cache
- **PK**: key
- **Columns**: key (primary), value, expiration

#### cache_locks
- **PK**: key
- **Columns**: key (primary), owner, expiration

#### jobs
- **PK**: id
- **Columns**: queue, payload, attempts, reserved_at, available_at, created_at
- **Indexes**: queue

#### sessions
- **PK**: id
- **Columns**: id (primary), user_id, ip_address, user_agent, payload, last_activity
- **FK**: user_id → users
- **Indexes**: user_id, last_activity

#### audits
- **PK**: id
- **Columns**: user_type, user_id, event, auditable_type, auditable_id, old_values, new_values, url, ip_address, user_agent, tags
- **Morph**: user (polymorphic), auditable (polymorphic)
- **Indexes**: [user_id, user_type]

---

### Auth Module (Shared)

#### password_reset_tokens
- **PK**: email
- **Columns**: user_id, email (primary), token, created_at

#### oauth_auth_codes
- **PK**: id
- **Columns**: id (primary, string 100), user_id, client_id, scopes, revoked, expires_at
- **FK**: user_id → users, client_id → oauth_clients
- **Indexes**: user_id

#### permissions (from spatie/laravel-permission)
- **PK**: id
- **Columns**: name, guard_name
- **Indexes**: unique [name, guard_name]
- **Soft Deletes**: Yes

#### roles (from spatie/laravel-permission)
- **PK**: id
- **Columns**: team_foreign_key (nullable), name, guard_name
- **Indexes**: unique [team_foreign_key, name, guard_name] (if teams enabled) or unique [name, guard_name]
- **Soft Deletes**: Yes

#### model_has_permissions (from spatie/laravel-permission)
- **PK**: [permission_id, model_morph_key, model_type] (or with team_foreign_key if teams enabled)
- **FK**: permission_id → permissions
- **Morph**: model (polymorphic)

#### model_has_roles (from spatie/laravel-permission)
- **PK**: [role_id, model_morph_key, model_type] (or with team_foreign_key if teams enabled)
- **FK**: role_id → roles
- **Morph**: model (polymorphic)

#### role_has_permissions (from spatie/laravel-permission)
- **PK**: [permission_id, role_id]
- **FK**: permission_id → permissions, role_id → roles

#### user_metas
- **PK**: id
- **Columns**: user_id, meta_key, meta_value
- **Soft Deletes**: Yes

#### permission_groups
- **PK**: id
- **Columns**: name, guard_name, description
- **Indexes**: unique [name, guard_name]
- **Soft Deletes**: Yes

#### permission_group_has_permissions
- **PK**: [permission_group_id, permission_id]
- **FK**: permission_group_id → permission_groups, permission_id → permissions

#### role_has_permission_groups
- **PK**: [role_id, permission_group_id]
- **FK**: role_id → roles, permission_group_id → permission_groups

---

### Development Module (Shared)

#### configurations
- **PK**: id
- **Columns**: configuration_key (unique), configuration_value, description, input_type, type_id, is_encrypted, is_system, is_visible
- **Indexes**: configuration_key, type_id
- **Soft Deletes**: Yes

---

### Email Module (Shared)

#### email_templates
- **PK**: id
- **Columns**: name, description, subject, body, status
- **Soft Deletes**: Yes

#### email_template_logs
- **PK**: id
- **Columns**: name, subject, body

#### email_campaigns
- **PK**: id
- **Columns**: email_template_id, name, subject, body, status, scheduled_at
- **FK**: email_template_id → email_templates
- **Soft Deletes**: Yes

#### email_groups
- **PK**: id
- **Columns**: name, description, status
- **Soft Deletes**: Yes

#### email_recipients
- **PK**: id
- **Columns**: email, status
- **Soft Deletes**: Yes

#### email_recipient_groups
- **PK**: id
- **Columns**: email_recipient_id, email_group_id
- **Soft Deletes**: Yes

#### email_subscribers
- **PK**: id
- **Columns**: email (unique), status
- **Soft Deletes**: Yes

#### email_attachments
- **PK**: id
- **Columns**: email_campaign_id, email_template_log_id, file_id
- **FK**: email_campaign_id → email_campaigns, email_template_log_id → email_template_logs, file_id → files
- **Soft Deletes**: Yes

#### email_recipient_metas
- **PK**: id
- **Columns**: email_recipient_id, meta_key, meta_value
- **FK**: email_recipient_id → email_recipients
- **Soft Deletes**: Yes

#### email_logs
- **PK**: id
- **Columns**: email_recipient_id, email_template_log_id, email_campaign_id, email_credential_id, email, status, subject, body, error_message, email_recipient_meta, opened_at, clicked_at
- **FK**: email_recipient_id → email_recipients, email_template_log_id → email_template_logs, email_campaign_id → email_campaigns, email_credential_id → email_credentials
- **Soft Deletes**: Yes

---

### FileManager Module (Shared)

#### folders
- **PK**: id
- **Columns**: name, description, parent_id, status
- **FK**: parent_id → folders (null on delete)
- **Soft Deletes**: Yes

#### files
- **PK**: id
- **Columns**: folder_id, hash_name, checksum, original_name, mime_type, host, status, access_level, size, metadata, is_encrypted
- **FK**: folder_id → folders (null on delete)
- **Indexes**: hash_name, status, access_level
- **Soft Deletes**: Yes

---

### Localization Module (Shared)

#### translations
- **PK**: id
- **Columns**: language_id, translation_key, translation_value, translation_context, is_shareable
- **FK**: language_id → languages (landlord, no FK constraint)
- **Indexes**: language_id
- **Soft Deletes**: Yes

#### translation_objects
- **PK**: id
- **Columns**: object_type, object_id, translation_id
- **FK**: translation_id → translations
- **Soft Deletes**: Yes

---

### Notification Module (Shared)

#### notifications
- **PK**: id
- **Columns**: user_id, module_id, name, title, body, type, route, priority, icon, metadata, seen_at, is_read, data
- **FK**: user_id → users, module_id → modules
- **Indexes**: [user_id, is_read], [user_id, created_at], [type, created_at]
- **Soft Deletes**: Yes

#### notification_channels
- **PK**: id
- **Columns**: user_id, channel_type, subscription_data, is_active, subscribed_at, last_used_at
- **FK**: user_id → users (cascade delete)
- **Indexes**: [user_id, channel_type], [user_id, is_active], unique [user_id, channel_type]

---

## Database Migration Paths

### Shared Migrations
- Main: `backend/database/migrations/shared/`
- Modules: `backend/modules/*/database/migrations/shared/`

## Cross-Database Relationships

- **translations.language_id** → languages (landlord database, no FK constraint)
