# Tenant Database ERD

This document contains the tenant database schema for the SaaS Dashboard application.

---

## Core Tables

#### branches
- **PK**: id
- **Columns**: name, code (unique), description, address, city, state, country, postal_code, phone, email, website, manager_name, manager_email, manager_phone, latitude, longitude, status, brand_id, working_hours, working_days, created_by, updated_by
- **FK**: brand_id → brands (landlord), created_by → users, updated_by → users
- **Indexes**: [brand_id, status], [brand_id, name], code, [city, state, country]
- **Soft Deletes**: Yes

#### tenant_settings
- **PK**: id
- **Columns**: key (unique), value

#### brands
- **PK**: id
- **Columns**: name, logo, description, website, email, phone, address, status, created_by, updated_by
- **FK**: created_by → users, updated_by → users
- **Indexes**: [status, name], created_by
- **Soft Deletes**: Yes

#### brand_module
- **PK**: id
- **Columns**: brand_id, module_id, module_key, color_palette, module_config, subscribed_at, created_by, updated_by
- **FK**: brand_id → brands, created_by → users, updated_by → users
- **Indexes**: [brand_id, module_id], module_key, status, unique [brand_id, module_id]
- **Soft Deletes**: Yes

#### webhooks
- **PK**: id
- **Columns**: name, url, secret, events, status, timeout, retry_count, headers, created_by
- **FK**: created_by → users
- **Indexes**: status, created_by
- **Soft Deletes**: Yes

#### webhook_logs
- **PK**: id
- **Columns**: webhook_id, event, payload, status_code, response, error, attempt, delivered_at
- **FK**: webhook_id → webhooks (cascade delete)
- **Indexes**: [webhook_id, created_at], event

#### import_history
- **PK**: id
- **Columns**: type, imported_count, failed_count, errors, filename, created_by
- **FK**: created_by → users
- **Indexes**: [created_by, created_at], type

#### feature_flags
- **PK**: id
- **Columns**: name, slug (unique), description, is_enabled

#### projects
- **PK**: id
- **Columns**: name, description, status, start_date, end_date, budget, priority, manager_id, metadata
- **FK**: manager_id → users
- **Indexes**: status, manager_id, [start_date, end_date]

#### tasks
- **PK**: id
- **Columns**: title, description, status, priority, project_id, assigned_to, created_by, due_date, completed_at, tags, attachments
- **FK**: project_id → projects, assigned_to → users, created_by → users
- **Indexes**: [status, priority], [project_id, status], due_date

#### organizations
- **PK**: id
- **Columns**: name, description, type, industry, size, website, contact_email, phone, address, city, state, country, postal_code, social_links, business_registration, status, annual_revenue, employee_count, founded_date
- **Indexes**: [type, industry], [country, state], status

#### departments
- **PK**: id
- **Columns**: (empty - placeholder)

#### teams
- **PK**: id
- **Columns**: (empty - placeholder)

---

### Accounting Module

#### chart_of_accounts
- **PK**: id
- **Columns**: code (unique), name, description, type, sub_type, parent_id, level, is_active, is_leaf, reconcile, currency, opening_balance, current_balance, created_by, custom_fields
- **FK**: parent_id → chart_of_accounts
- **Indexes**: [type, is_active], [parent_id, level], code
- **Soft Deletes**: Yes

#### journal_entries
- **PK**: id
- **Columns**: entry_number (unique), entry_date, state, reference, description, total_debit, total_credit, currency, fiscal_year_id, created_by, posted_by, posted_at, custom_fields
- **FK**: fiscal_year_id → fiscal_years, created_by → users, posted_by → users
- **Indexes**: [state, entry_date], [fiscal_year_id, entry_date], entry_number
- **Soft Deletes**: Yes

#### journal_items
- **PK**: id
- **Columns**: journal_entry_id, account_id, debit, credit, description, reference, custom_fields
- **Indexes**: [journal_entry_id, account_id], account_id
- **Soft Deletes**: Yes

#### fiscal_years
- **PK**: id
- **Columns**: name, start_date, end_date, is_active, is_closed, closing_date, description, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [is_active, start_date], [start_date, end_date]
- **Soft Deletes**: Yes

---

### Auth Module (Tenant)

#### users
- **PK**: id
- **Columns**: customer_id, name, email (unique), username (unique, nullable), country_id, language_id, factor_authenticate, google2fa_secret, password, remember_token, last_password_at, email_verified_at
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

### CRM Module

#### leads
- **PK**: id
- **Columns**: name, email, phone, company, title, description, status, source, expected_revenue, expected_close_date, assigned_to, created_by, custom_fields
- **FK**: assigned_to → users, created_by → users
- **Indexes**: [status, assigned_to], [source, created_at], expected_close_date
- **Soft Deletes**: Yes

#### opportunities
- **PK**: id
- **Columns**: name, lead_id, contact_id, company_id, description, stage, expected_revenue, probability, expected_close_date, actual_close_date, assigned_to, created_by, custom_fields
- **FK**: lead_id → leads, contact_id → contacts, company_id → companies, assigned_to → users, created_by → users
- **Indexes**: [stage, assigned_to], [expected_close_date, stage], lead_id, contact_id, company_id
- **Soft Deletes**: Yes

#### contacts
- **PK**: id
- **Columns**: first_name, last_name, email, phone, mobile, title, company_id, address, city, state, postal_code, country, birthday, notes, type, assigned_to, created_by, custom_fields
- **FK**: company_id → companies, assigned_to → users, created_by → users
- **Indexes**: [company_id, type], [assigned_to, type], email, phone
- **Soft Deletes**: Yes

#### companies
- **PK**: id
- **Columns**: name, email, phone, website, industry, employee_count, annual_revenue, address, city, state, postal_code, country, description, notes, type, assigned_to, created_by, custom_fields
- **FK**: assigned_to → users, created_by → users
- **Indexes**: [type, industry], assigned_to, name, email
- **Soft Deletes**: Yes

#### activities
- **PK**: id
- **Columns**: subject, description, type, status, due_date, completed_at, related_type, related_id, assigned_to, created_by, custom_fields
- **FK**: assigned_to → users, created_by → users
- **Morph**: related (polymorphic)
- **Indexes**: [type, status], [assigned_to, due_date], due_date
- **Soft Deletes**: Yes

---

### Customer Module (Tenant)

#### brands
- **PK**: id
- **Columns**: logo, name, slug (unique), description, website, email, phone, address, status, created_by, updated_by
- **FK**: created_by → users, updated_by → users
- **Indexes**: slug, name, status
- **Soft Deletes**: Yes

#### brand_modules
- **PK**: id
- **Columns**: brand_id, module_id (nullable), module_key, status, color_palette, module_config, subscribed_at, created_by, updated_by
- **FK**: brand_id → brands, created_by → users, updated_by → users
- **Indexes**: [brand_id, module_id], module_key, status, unique [brand_id, module_key]
- **Soft Deletes**: Yes

---

### HR Module

#### employees
- **PK**: id
- **Columns**: employee_number (unique), user_id, first_name, last_name, email, phone, date_of_birth, gender, national_id, passport_number, address, city, state, postal_code, country, hire_date, termination_date, employment_status, job_title, department, manager_id, salary, currency, pay_frequency, emergency_contact_name, emergency_contact_phone, emergency_contact_relationship, created_by, custom_fields
- **FK**: user_id → users, manager_id → employees, created_by → users
- **Indexes**: [employment_status, hire_date], [department, job_title], [manager_id, employment_status], employee_number
- **Soft Deletes**: Yes

#### attendances
- **PK**: id
- **Columns**: employee_id, date, check_in, check_out, break_start, break_end, total_hours, break_duration, overtime_hours, status, notes, is_approved, approved_by, approved_at, created_by, custom_fields
- **FK**: employee_id → employees, approved_by → users, created_by → users
- **Indexes**: [employee_id, date], [date, status], [is_approved, date], unique [employee_id, date]
- **Soft Deletes**: Yes

#### payrolls
- **PK**: id
- **Columns**: payroll_number (unique), employee_id, pay_period_start, pay_period_end, pay_date, status, basic_salary, overtime_pay, bonus, allowances, gross_pay, tax_deduction, social_security, health_insurance, other_deductions, total_deductions, net_pay, currency, notes, created_by, approved_by, approved_at, custom_fields
- **FK**: employee_id → employees, created_by → users, approved_by → users
- **Indexes**: [employee_id, pay_period_start], [status, pay_date], [pay_period_start, pay_period_end], payroll_number
- **Soft Deletes**: Yes

#### leave_requests
- **PK**: id
- **Columns**: employee_id, leave_type, start_date, end_date, total_days, reason, status, approved_by, approved_at, approval_notes, rejection_reason, is_emergency, attachments, created_by, custom_fields
- **FK**: employee_id → employees, approved_by → users, created_by → users
- **Indexes**: [employee_id, start_date], [status, leave_type], [start_date, end_date], approved_by
- **Soft Deletes**: Yes

---

### Inventory Module

#### warehouses
- **PK**: id
- **Columns**: name, code (unique), description, address, city, state, postal_code, country, phone, email, is_active, is_default, latitude, longitude, manager_id, custom_fields
- **FK**: manager_id → users
- **Indexes**: [is_active, is_default], code, manager_id
- **Soft Deletes**: Yes

#### stock_moves
- **PK**: id
- **Columns**: reference, product_id, warehouse_id, move_type, origin_type, origin_id, quantity, unit_cost, total_cost, date, state, description, created_by, custom_fields
- **FK**: warehouse_id → warehouses, created_by → users
- **Indexes**: [product_id, warehouse_id], [move_type, date], [origin_type, origin_id], [state, date]
- **Soft Deletes**: Yes

#### stock_valuations
- **PK**: id
- **Columns**: product_id, warehouse_id, quantity, unit_cost, total_cost, valuation_method, valuation_date, reference, description, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [product_id, warehouse_id], [valuation_date, valuation_method], unique [product_id, warehouse_id, valuation_date]
- **Soft Deletes**: Yes

#### reorder_rules
- **PK**: id
- **Columns**: product_id, warehouse_id, min_quantity, max_quantity, reorder_quantity, rule_type, is_active, lead_time_days, safety_stock, description, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [product_id, warehouse_id], [is_active, rule_type], unique [product_id, warehouse_id]
- **Soft Deletes**: Yes

---

### Reporting Module

#### dashboards
- **PK**: id
- **Columns**: name, description, widgets, layout, is_default, is_public, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [created_by, is_default], is_public
- **Soft Deletes**: Yes

#### reports
- **PK**: id
- **Columns**: name, description, type, module, query, filters, columns, chart_config, is_scheduled, schedule_frequency, schedule_config, is_public, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [module, type], [created_by, is_public], is_scheduled
- **Soft Deletes**: Yes

#### report_templates
- **PK**: id
- **Columns**: name, description, module, category, template_config, default_filters, default_columns, is_system, is_active, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [module, category], [is_system, is_active]
- **Soft Deletes**: Yes

---

### Sales Module

#### products
- **PK**: id
- **Columns**: name, sku (unique), description, price, cost, stock_quantity, min_stock_level, category, brand, unit, weight, dimensions, is_active, is_digital, images, attributes, custom_fields
- **Indexes**: [category, is_active], [brand, is_active], sku, stock_quantity
- **Soft Deletes**: Yes

#### quotations
- **PK**: id
- **Columns**: quotation_number (unique), contact_id, company_id, opportunity_id, quotation_date, valid_until, status, subtotal, tax_amount, discount_amount, total_amount, currency, tax_rate, notes, terms_conditions, created_by, assigned_to, custom_fields
- **FK**: contact_id → contacts, company_id → companies, opportunity_id → opportunities, created_by → users, assigned_to → users
- **Indexes**: [status, quotation_date], [contact_id, status], [company_id, status], quotation_number
- **Soft Deletes**: Yes

#### orders
- **PK**: id
- **Columns**: order_number (unique), quotation_id, contact_id, company_id, order_date, delivery_date, status, payment_status, subtotal, tax_amount, discount_amount, shipping_amount, total_amount, currency, tax_rate, shipping_address, billing_address, notes, created_by, assigned_to, custom_fields
- **FK**: quotation_id → quotations, contact_id → contacts, company_id → companies, created_by → users, assigned_to → users
- **Indexes**: [status, order_date], [payment_status, order_date], [contact_id, status], [company_id, status], order_number
- **Soft Deletes**: Yes

#### invoices
- **PK**: id
- **Columns**: invoice_number (unique), order_id, contact_id, company_id, invoice_date, due_date, status, subtotal, tax_amount, discount_amount, total_amount, paid_amount, balance_amount, currency, tax_rate, notes, terms_conditions, created_by, assigned_to, custom_fields
- **FK**: order_id → orders, contact_id → contacts, company_id → companies, created_by → users, assigned_to → users
- **Indexes**: [status, invoice_date], [due_date, status], [contact_id, status], [company_id, status], invoice_number
- **Soft Deletes**: Yes

---

### Ticket Module

#### tickets
- **PK**: id
- **Columns**: ticket_number (unique), title, description, html_content, status, priority, created_by, assigned_to, brand_id, tags, due_date, resolved_at, closed_at, sla_data, metadata
- **FK**: created_by → users (cascade delete), assigned_to → users (set null), brand_id → brands
- **Indexes**: [status, priority], [created_by, status], [assigned_to, status], brand_id, ticket_number
- **Soft Deletes**: Yes

#### ticket_status_logs
- **PK**: id
- **Columns**: ticket_id, old_status, new_status, changed_by, comment, time_in_previous_status, metadata
- **FK**: ticket_id → tickets (cascade delete), changed_by → users (cascade delete)
- **Indexes**: [ticket_id, created_at], [changed_by, created_at], new_status

---

## Database Migration Paths

### Tenant Migrations
- Main: `backend/database/migrations/tenant/`
- Modules: `backend/modules/*/database/migrations/tenant/`

## Cross-Database Relationships

- **branches.brand_id** → brands (landlord database)
