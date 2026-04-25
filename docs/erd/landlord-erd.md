# Landlord Database ERD

This document contains the landlord database schema for the SaaS Dashboard application.

---

## Core Tables

#### tenants
- **PK**: id
- **Columns**: name, domain (unique), database (unique)
- **Indexes**: domain, database

#### telescope_entries
- **PK**: sequence
- **Columns**: uuid (unique), batch_id, family_hash, should_display_on_index, type, content, created_at
- **Indexes**: batch_id, family_hash, created_at, [type, should_display_on_index]

#### telescope_entries_tags
- **PK**: [entry_uuid, tag]
- **FK**: entry_uuid → telescope_entries.uuid (cascade delete)
- **Columns**: entry_uuid, tag
- **Indexes**: tag

#### telescope_monitoring
- **PK**: tag
- **Columns**: tag

#### feature_flags
- **PK**: id
- **Columns**: name, slug (unique), description, is_enabled
- **Indexes**: slug

#### documentation
- **PK**: id
- **Columns**: title, slug (unique), content, category, sort_order, is_published
- **Indexes**: slug
- **Soft Deletes**: Yes

#### tickets
- **PK**: id
- **Columns**: ticket_number (unique), title, description, html_content, status, priority, created_by, assigned_to, brand_id, tags, due_date, resolved_at, closed_at, sla_data, metadata
- **FK**: created_by → users, assigned_to → users, brand_id → brands
- **Indexes**: ticket_number
- **Soft Deletes**: Yes

#### plans
- **PK**: id
- **Columns**: name, slug (unique), description, features_summary, sort_order, is_popular, is_custom, metadata, status
- **Indexes**: slug
- **Soft Deletes**: Yes

---

### API Module

#### api_keys
- **PK**: id
- **Columns**: name, key (unique), secret, user_id, status, permissions, scopes, last_used_at, expires_at, ip_whitelist, rate_limit, rate_limit_period, description, created_by, custom_fields
- **FK**: user_id → users, created_by → users
- **Indexes**: [key, status], [user_id, status], last_used_at, expires_at
- **Soft Deletes**: Yes

#### api_rate_limits
- **PK**: id
- **Columns**: identifier, type, endpoint, requests_count, window_start, limit, period, reset_at, is_blocked, blocked_until, block_reason, custom_fields
- **Indexes**: [identifier, type, endpoint], [window_start, reset_at], is_blocked, blocked_until

#### api_logs
- **PK**: id
- **Columns**: api_key_id, user_id, method, endpoint, ip_address, user_agent, status_code, response_time_ms, request_headers, request_body, response_headers, response_body, error_message, metadata, logged_at
- **FK**: api_key_id → api_keys, user_id → users
- **Indexes**: [api_key_id, logged_at], [user_id, logged_at], [method, endpoint], [status_code, logged_at], ip_address, logged_at

---

### Auth Module

#### users
- **PK**: id
- **Columns**: name, email (unique), username (unique, nullable), country_id, language_id, factor_authenticate, google2fa_secret, password, remember_token, last_password_at, email_verified_at
- **Indexes**: email, username
- **Soft Deletes**: Yes

#### oauth_access_tokens
- **PK**: id (string 100)
- **Columns**: user_id, client_id, name, scopes, revoked, expires_at
- **FK**: user_id → users, client_id → oauth_clients
- **Indexes**: user_id

#### oauth_refresh_tokens
- **PK**: id (string 100)
- **Columns**: access_token_id, revoked, expires_at
- **FK**: access_token_id → oauth_access_tokens
- **Indexes**: access_token_id

#### oauth_clients
- **PK**: id
- **Columns**: user_id, name, secret, provider, redirect, personal_access_client, password_client, revoked
- **FK**: user_id → users
- **Indexes**: user_id

#### oauth_personal_access_clients
- **PK**: id
- **Columns**: client_id
- **FK**: client_id → oauth_clients

#### personal_access_tokens
- **PK**: id
- **Columns**: tokenable_type, tokenable_id, name, token (unique), abilities, last_used_at, expires_at
- **Morph**: tokenable (polymorphic)

#### email_tokens
- **PK**: id
- **Columns**: user_id, token, status, expired_at

#### login_attempts
- **PK**: id
- **Columns**: user_id, agent, ip

#### factor_authenticate_tokens
- **PK**: id
- **Columns**: user_id, token_id

---

### Customer Module

#### customers
- **PK**: id
- **Columns**: name, username (unique), tenant_id (unique), category_id
- **Indexes**: username, tenant_id
- **Soft Deletes**: Yes

#### brands
- **PK**: id
- **Columns**: logo, name, slug (unique), description, tenant_id, created_by, updated_by
- **FK**: tenant_id → tenants, created_by → users, updated_by → users
- **Indexes**: [tenant_id, slug], [tenant_id, name]
- **Soft Deletes**: Yes

#### brand_module_subscriptions
- **PK**: id
- **Columns**: brand_id, module_key, module_name, subscription_status, subscription_start, subscription_end, module_config, created_by, updated_by
- **FK**: brand_id → brands, created_by → users, updated_by → users
- **Indexes**: [brand_id, module_key], subscription_status, unique [brand_id, module_key]
- **Soft Deletes**: Yes

---

### Development Module

#### backups
- **PK**: id
- **Columns**: name, metadata
- **Soft Deletes**: Yes

#### database_flows
- **PK**: id
- **Columns**: connection, table, position, color
- **Soft Deletes**: Yes

#### ip_blacklists
- **PK**: id
- **Columns**: ip_address
- **Soft Deletes**: Yes

---

### Email Module

#### email_credentials
- **PK**: id
- **Columns**: name, description, from_address, from_name, mailer, host, port, username, password, encryption, status
- **Soft Deletes**: Yes

---

### Geography Module

#### cities
- **PK**: id
- **Columns**: name, province_id, postal_code, is_capital, phone_code, timezone, latitude, longitude, area_km2, population, elevation_m
- **Soft Deletes**: Yes

#### countries
- **PK**: id
- **Columns**: name, code (unique), region, flag, timezone, phone_code, latitude, longitude, currency_code, currency_symbol, language_code, area_km2, population
- **Indexes**: code
- **Soft Deletes**: Yes

#### provinces
- **PK**: id
- **Columns**: name, country_id, is_capital, flag, timezone, phone_code, latitude, longitude, area_km2, population, currency_code, currency_symbol, language_code
- **Soft Deletes**: Yes

#### streets
- **PK**: id
- **Columns**: name, town_id, postal_code, latitude, longitude, area_km2, population, elevation_m, phone_code, timezone
- **Soft Deletes**: Yes

#### towns
- **PK**: id
- **Columns**: name, postalcode, city_id, latitude, longitude, area_km2, population, elevation_m
- **Soft Deletes**: Yes

---

### Localization Module

#### languages
- **PK**: id
- **Columns**: name, locale (unique), direction
- **Indexes**: locale
- **Soft Deletes**: Yes

---

### Payment Module

#### payment_methods
- **PK**: id
- **Columns**: name, description, processor_type, gateway_name, country_codes, supported_currencies, is_global, region_restrictions, status, authentication_type, priority, success_rate, average_processing_time, features, metadata
- **Indexes**: [status, is_global], [processor_type, status], priority

#### payment_method_currencies
- **PK**: id
- **Columns**: payment_method_id, currency_id, processing_currency_id, settlement_days, settlement_schedule, conversion_rate, auto_conversion, status
- **FK**: payment_method_id → payment_methods, currency_id → currencies, processing_currency_id → currencies
- **Indexes**: unique [payment_method_id, currency_id], [currency_id, status]

#### payment_method_fees
- **PK**: id
- **Columns**: payment_method_id, currency_id, fee_type, fee_value, min_fee, max_fee, fee_tiers, fixed_fee, applies_to, region, customer_segment, status, effective_from, effective_until
- **FK**: payment_method_id → payment_methods, currency_id → currencies
- **Indexes**: [payment_method_id, currency_id, applies_to], [status, effective_from, effective_until]

#### payment_method_limits
- **PK**: id
- **Columns**: payment_method_id, currency_id, limit_type, min_limit, max_limit, limit_duration, transaction_count_limit, customer_segment, region, status, conditions
- **FK**: payment_method_id → payment_methods, currency_id → currencies
- **Indexes**: [payment_method_id, currency_id, limit_type], [customer_segment, status]

#### payment_transactions
- **PK**: id
- **Columns**: transaction_id (unique), payment_method_id, currency_id, amount, base_currency_amount, exchange_rate_used, customer_id, merchant_account_id, gateway_transaction_id, gateway_reference, transaction_type, status, gateway_response, error_details, metadata, settlement_status, settlement_date, fees_breakdown, total_fees, net_amount, invoice_number, order_id, description, customer_ip, user_agent, billing_address, shipping_address, payment_method_details, is_test, processed_at, settled_at
- **FK**: payment_method_id → payment_methods, currency_id → currencies
- **Indexes**: [status, created_at], [customer_id, status], gateway_transaction_id, [transaction_type, status], [settlement_status, settlement_date], [is_test, status]

#### payment_method_configurations
- **PK**: id
- **Columns**: payment_method_id, environment, config_key, config_value, is_secret, config_type, description, is_required, validation_rules, status
- **FK**: payment_method_id → payment_methods
- **Indexes**: unique [payment_method_id, environment, config_key], [environment, status]

#### payment_gateway_logs
- **PK**: id
- **Columns**: payment_method_id, transaction_id, log_level, operation, request_data, response_data, endpoint_called, http_status, processing_time_ms, error_code, error_message, correlation_id, headers, gateway_request_id, is_webhook, ip_address
- **FK**: payment_method_id → payment_methods, transaction_id → payment_transactions
- **Indexes**: [payment_method_id, created_at], [log_level, created_at], [operation, created_at], correlation_id, [is_webhook, created_at]

#### customer_payment_methods
- **PK**: id
- **Columns**: customer_id, payment_method_id, gateway_token, gateway_customer_id, method_details, payment_type, last_four, brand, expiry_month, expiry_year, holder_name, billing_address, is_default, is_verified, status, verified_at, last_used_at, metadata
- **FK**: payment_method_id → payment_methods
- **Indexes**: [customer_id, status], [payment_method_id, status], gateway_token, [is_default, status]

#### refunds
- **PK**: id
- **Columns**: refund_id (unique), original_transaction_id, refund_transaction_id, amount, fee_refunded, refund_type, reason, reason_details, status, gateway_refund_id, gateway_response, initiated_by, processed_at, metadata
- **FK**: original_transaction_id → payment_transactions, refund_transaction_id → payment_transactions
- **Indexes**: [original_transaction_id, status], [status, created_at], [refund_type, status], gateway_refund_id

#### chargebacks
- **PK**: id
- **Columns**: chargeback_id (unique), transaction_id, amount, fee, reason_code, reason_description, status, gateway_case_id, evidence_due_date, evidence_submitted, resolution, resolution_notes, liability_shift_amount, gateway_response, received_at, resolved_at, metadata
- **FK**: transaction_id → payment_transactions
- **Indexes**: [transaction_id, status], [status, evidence_due_date], [resolution, resolved_at], gateway_case_id

#### payment_routing_rules
- **PK**: id
- **Columns**: name, description, conditions, priority, target_payment_method_id, fallback_payment_method_id, rule_type, traffic_percentage, time_restrictions, amount_restrictions, geographic_restrictions, customer_segment_restrictions, is_active, effective_from, effective_until, success_count, failure_count, success_rate, metadata
- **FK**: target_payment_method_id → payment_methods, fallback_payment_method_id → payment_methods
- **Indexes**: [is_active, priority], [rule_type, is_active], [effective_from, effective_until], [target_payment_method_id, is_active]

#### payment_audit_logs
- **PK**: id
- **Columns**: operation, entity_type, entity_id, user_id, ip_address, user_agent, session_id, data
- **Indexes**: [operation, created_at], [entity_type, entity_id], [user_id, created_at], created_at

---

### Static Pages Module

#### static_pages
- **PK**: id
- **Columns**: name, slug (unique), description, status, type, image, meta_title, meta_description, meta_keywords, is_public, author_id, revision, order, parent_id, custom_fields
- **FK**: author_id → users, parent_id → static_pages
- **Indexes**: [slug, status], [type, status], [is_public, status], [author_id, status], [parent_id, order], order
- **Soft Deletes**: Yes

#### static_page_attributes
- **PK**: id
- **Columns**: static_page_id, key, value, language_code, status, metadata
- **FK**: static_page_id → static_pages (cascade delete)
- **Indexes**: [static_page_id, key, language_code], [static_page_id, status], [language_code, status], key
- **Soft Deletes**: Yes

---

### Subscription Module

#### plans
- **PK**: id
- **Columns**: name, slug (unique), description, features_summary, sort_order, is_popular, is_custom, metadata, status
- **Indexes**: slug
- **Soft Deletes**: Yes

#### plan_prices
- **PK**: id
- **Columns**: plan_id, currency_id, country_code, price, setup_fee, billing_cycle, billing_interval, valid_from, valid_until, metadata, status
- **FK**: plan_id → plans, currency_id → currencies
- **Indexes**: unique [plan_id, currency_id, country_code, billing_cycle], [plan_id, status], [currency_id, country_code], [valid_from, valid_until]

#### plan_prices_by_users
- **PK**: id
- **Columns**: plan_id, currency_id, country_code, min_users, max_users, price_per_user, base_price, billing_cycle, pricing_model, tier_discounts, valid_from, valid_until, metadata, status
- **FK**: plan_id → plans, currency_id → currencies
- **Indexes**: [plan_id, min_users, max_users], [currency_id, country_code], [billing_cycle, status], [valid_from, valid_until]

#### plan_features
- **PK**: id
- **Columns**: plan_id, feature_key, name, description, feature_type, feature_value, numeric_limit, is_unlimited, unit, sort_order, is_highlighted, metadata, status
- **FK**: plan_id → plans
- **Indexes**: unique [plan_id, feature_key], [plan_id, status, sort_order], feature_key

#### plan_discounts
- **PK**: id
- **Columns**: plan_id, name, code (unique, nullable), description, discount_type, discount_value, applies_to, cycle_count, usage_limit, usage_limit_per_customer, usage_count, minimum_amount, applicable_countries, applicable_currencies, start_date, end_date, is_stackable, metadata, status
- **FK**: plan_id → plans
- **Indexes**: [plan_id, status], [code, status], [start_date, end_date], usage_count

#### plan_subscriptions
- **PK**: id
- **Columns**: subscription_id (unique), brand_id, user_id, plan_id, currency_id, country_code, price, setup_fee, billing_cycle, billing_interval, user_count, trial_starts_at, trial_ends_at, starts_at, ends_at, next_billing_at, canceled_at, expires_at, cancellation_reason, cancellation_feedback, applied_discounts, subscription_data, status, auto_renew
- **FK**: brand_id → brands, user_id → users, plan_id → plans, currency_id → currencies
- **Indexes**: [brand_id, status], [user_id, status], [plan_id, status], [status, next_billing_at], [trial_ends_at, status], [expires_at, status]
- **Soft Deletes**: Yes

#### plan_price_history
- **PK**: id
- **Columns**: plan_id, currency_id, country_code, billing_cycle, old_price, new_price, old_setup_fee, new_setup_fee, change_date, effective_date, change_type, change_reason, changed_by, metadata, status
- **FK**: plan_id → plans, currency_id → currencies, changed_by → users
- **Indexes**: [plan_id, change_date], [currency_id, country_code], [change_date, effective_date], [change_type, status]

#### plan_billing_cycles
- **PK**: id
- **Columns**: plan_id, currency_id, country_code, billing_cycle, billing_interval, custom_period, price, setup_fee, discount_percentage, is_default, is_popular, sort_order, description, metadata, status
- **FK**: plan_id → plans, currency_id → currencies
- **Indexes**: unique [plan_id, currency_id, country_code, billing_cycle], [plan_id, status, sort_order], [billing_cycle, status]

#### plan_trials
- **PK**: id
- **Columns**: plan_id, country_code, trial_days, requires_payment_method, auto_convert, trial_type, trial_price, trial_features, trial_limits, trial_terms, allow_multiple_trials, grace_period_days, metadata, status
- **FK**: plan_id → plans
- **Indexes**: unique [plan_id, country_code], [plan_id, status], [trial_days, status]

#### plan_upgrade_rules
- **PK**: id
- **Columns**: from_plan_id, to_plan_id, rule_type, is_allowed, proration_type, upgrade_fee, downgrade_credit, restriction_days, max_changes_per_period, required_conditions, change_description, user_message, requires_approval, metadata, status
- **FK**: from_plan_id → plans, to_plan_id → plans
- **Indexes**: unique [from_plan_id, to_plan_id], [from_plan_id, rule_type, status], [to_plan_id, rule_type, status]

#### subscription_invoices
- **PK**: id
- **Columns**: invoice_number (unique), brand_id, subscription_id, user_id, plan_id, currency_id, country_code, invoice_type, subtotal, discount_amount, tax_amount, total_amount, line_items, applied_discounts, tax_breakdown, invoice_date, due_date, period_start, period_end, paid_at, voided_at, notes, external_invoice_id, billing_address, metadata, status
- **FK**: brand_id → brands, subscription_id → plan_subscriptions, user_id → users, plan_id → plans, currency_id → currencies
- **Indexes**: [brand_id, status], [subscription_id, status], [user_id, status], [invoice_date, due_date], [status, due_date], invoice_number

#### subscription_payments
- **PK**: id
- **Columns**: subscription_id, invoice_id, payment_transaction_id, payment_id (unique), external_payment_id, amount, currency_id, payment_type, payment_method_type, payment_method_details, attempted_at, processed_at, failed_at, refunded_at, failure_reason, retry_count, next_retry_at, gateway_response, metadata, status
- **FK**: subscription_id → plan_subscriptions, invoice_id → subscription_invoices, payment_transaction_id → payment_transactions, currency_id → currencies
- **Indexes**: [subscription_id, status], [invoice_id, status], payment_transaction_id, [status, attempted_at], [status, next_retry_at], payment_id

#### subscriptions (legacy)
- **PK**: id
- **Columns**: brand_id, tenant_id, user_id, plan_id, start_date, end_date, status, price, currency_id, canceled_at, cancellation_reason
- **FK**: brand_id → brands, tenant_id → tenants, user_id → users, plan_id → plans, currency_id → currencies
- **Indexes**: brand_id
- **Soft Deletes**: Yes

---

### Utilities Module

#### currencies
- **PK**: id
- **Columns**: code (unique), name, symbol, decimal_places, exchange_rate, exchange_rate_last_updated, symbol_position, base_currency, priority, note, status
- **Indexes**: code
- **Soft Deletes**: Yes

#### categories
- **PK**: id
- **Columns**: name, slug (unique), description, parent_id, icon, priority, status
- **FK**: parent_id → categories (cascade delete)
- **Soft Deletes**: Yes

#### tags
- **PK**: id
- **Columns**: name, parent_id, slug (unique), description, status, priority, icon
- **Soft Deletes**: Yes

#### announcements
- **PK**: id
- **Columns**: name, description, body, start_at, end_at
- **Soft Deletes**: Yes

#### types
- **PK**: id
- **Columns**: name, slug (unique), description, status, priority, icon
- **Soft Deletes**: Yes

#### industries
- **PK**: id
- **Columns**: name, slug (unique), description, status, priority, icon
- **Soft Deletes**: Yes

#### modules
- **PK**: id
- **Columns**: module_key (unique), name, description, route, icon, slogan, status
- **Indexes**: module_key
- **Soft Deletes**: Yes

#### releases
- **PK**: id
- **Columns**: object_model, object_id, name, slug (unique), description, body, version, status, release_date
- **Soft Deletes**: Yes

#### entities
- **PK**: id
- **Columns**: entity_path, entity_name
- **Soft Deletes**: Yes

#### module_entities
- **PK**: id
- **Columns**: module_id, entity_id
- **Soft Deletes**: Yes

#### units
- **PK**: id
- **Columns**: name, code, type_id, base_conversion, description, is_base_unit
- **Soft Deletes**: Yes

---

### Workflow Module

#### workflow_definitions
- **PK**: id
- **Columns**: name, description, module, trigger_event, steps, conditions, is_active, priority, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [module, trigger_event], [is_active, priority]
- **Soft Deletes**: Yes

#### workflow_instances
- **PK**: id
- **Columns**: workflow_definition_id, related_type, related_id, status, current_step, context, variables, started_at, completed_at, error_message, created_by, custom_fields
- **FK**: workflow_definition_id → workflow_definitions, created_by → users
- **Morph**: related (polymorphic)
- **Indexes**: [workflow_definition_id, status], status
- **Soft Deletes**: Yes

#### workflow_steps
- **PK**: id
- **Columns**: workflow_instance_id, step_number, step_type, step_name, step_config, status, input_data, output_data, error_message, started_at, completed_at, custom_fields
- **FK**: workflow_instance_id → workflow_instances
- **Indexes**: [workflow_instance_id, step_number], [status, step_type]
- **Soft Deletes**: Yes

---

## Database Migration Paths

### Landlord Migrations
- Main: `backend/database/migrations/landlord/`
- Modules: `backend/modules/*/database/migrations/landlord/`
