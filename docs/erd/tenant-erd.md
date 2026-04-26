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

#### crm_leads
- **PK**: id
- **Columns**: name, email, phone, company, title, description, status, source, expected_revenue, expected_close_date, assigned_to, created_by, custom_fields
- **FK**: assigned_to → users, created_by → users
- **Indexes**: [status, assigned_to], [source, created_at], expected_close_date
- **Soft Deletes**: Yes

#### crm_opportunities
- **PK**: id
- **Columns**: name, lead_id, contact_id, company_id, description, stage, expected_revenue, probability, expected_close_date, actual_close_date, assigned_to, created_by, custom_fields
- **FK**: lead_id → crm_leads, contact_id → crm_contacts, company_id → crm_companies, assigned_to → users, created_by → users
- **Indexes**: [stage, assigned_to], [expected_close_date, stage], lead_id, contact_id, company_id
- **Soft Deletes**: Yes

#### crm_contacts
- **PK**: id
- **Columns**: first_name, last_name, email, phone, mobile, title, company_id, address, city, state, postal_code, country, birthday, notes, type, assigned_to, created_by, custom_fields
- **FK**: company_id → crm_companies, assigned_to → users, created_by → users
- **Indexes**: [company_id, type], [assigned_to, type], email, phone
- **Soft Deletes**: Yes

#### crm_companies
- **PK**: id
- **Columns**: name, email, phone, website, industry, employee_count, annual_revenue, address, city, state, postal_code, country, description, notes, type, assigned_to, created_by, custom_fields
- **FK**: assigned_to → users, created_by → users
- **Indexes**: [type, industry], assigned_to, name, email
- **Soft Deletes**: Yes

#### crm_activities
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

#### hr_departments
- **PK**: id
- **Columns**: name, code (unique, nullable), parent_id, manager_id, description, status, created_by, custom_fields
- **FK**: parent_id → hr_departments (null on delete), manager_id → hr_employees (null on delete), created_by → users (null on delete)
- **Indexes**: status, parent_id
- **Soft Deletes**: Yes

#### hr_positions
- **PK**: id
- **Columns**: title, code (unique, nullable), department_id, level, min_salary, max_salary, description, requirements, is_active, created_by, custom_fields
- **FK**: department_id → hr_departments (null on delete), created_by → users (null on delete)
- **Indexes**: department_id, level, is_active
- **Soft Deletes**: Yes

#### hr_employees
- **PK**: id
- **Columns**: employee_number (unique), user_id (nullable), first_name, middle_name (nullable), last_name, email (unique), phone (nullable), date_of_birth (nullable), gender (nullable), marital_status (nullable), national_id (nullable), passport_number (nullable), address (nullable), city (nullable), state (nullable), postal_code (nullable), country (nullable), hire_date, probation_end_date (nullable), termination_date (nullable), employment_status, employment_type, department_id (nullable), position_id (nullable), manager_id (nullable), salary (nullable), currency, pay_frequency, emergency_contact_name (nullable), emergency_contact_phone (nullable), emergency_contact_relationship (nullable), avatar (nullable), created_by, custom_fields
- **FK**: user_id → users (null on delete), department_id → hr_departments (null on delete), position_id → hr_positions (null on delete), manager_id → hr_employees (null on delete), created_by → users (null on delete)
- **Indexes**: employee_number, user_id, employment_status, employment_type, department_id, position_id, manager_id, hire_date
- **Soft Deletes**: Yes

#### hr_employee_documents
- **PK**: id
- **Columns**: employee_id, type, title, file_path, file_name, file_size, mime_type, issued_date (nullable), expiry_date (nullable), issued_by (nullable), document_number (nullable), notify_before_days (nullable), notes (nullable), created_by, custom_fields
- **FK**: employee_id → hr_employees (cascade delete), created_by → users (null on delete)
- **Indexes**: employee_id, type, expiry_date
- **Soft Deletes**: Yes

#### hr_employee_contracts
- **PK**: id
- **Columns**: employee_id, contract_number (unique), type, status, start_date, end_date (nullable), basic_salary, currency, benefits (json), file_path (nullable), notes (nullable), created_by, custom_fields
- **FK**: employee_id → hr_employees (cascade delete), created_by → users (null on delete)
- **Indexes**: employee_id, status, end_date
- **Soft Deletes**: Yes

#### hr_employment_history
- **PK**: id
- **Columns**: employee_id, change_type, from_value (nullable), to_value (nullable), effective_date, notes (nullable), created_by
- **FK**: employee_id → hr_employees (cascade delete), created_by → users (null on delete)
- **Indexes**: employee_id, change_type, effective_date

#### hr_work_shifts
- **PK**: id
- **Columns**: name, start_time, end_time, break_minutes, working_days (json), grace_minutes (nullable), description (nullable), is_active, created_by, custom_fields
- **FK**: created_by → users (null on delete)
- **Indexes**: is_active
- **Soft Deletes**: Yes

#### hr_employee_work_schedules
- **PK**: id
- **Columns**: employee_id, work_shift_id, effective_from, effective_to (nullable), created_by
- **FK**: employee_id → hr_employees (cascade delete), work_shift_id → hr_work_shifts (cascade delete), created_by → users (null on delete)
- **Indexes**: employee_id, work_shift_id, effective_from
- **Soft Deletes**: Yes

#### hr_attendances
- **PK**: id
- **Columns**: employee_id, date, check_in (nullable), check_out (nullable), break_start (nullable), break_end (nullable), total_hours (nullable), break_duration (nullable), overtime_hours (nullable), status, source, ip_address (nullable), latitude (nullable), longitude (nullable), notes (nullable), is_approved, approved_by (nullable), approved_at (nullable), created_by, custom_fields
- **FK**: employee_id → hr_employees (cascade delete), approved_by → users (null on delete), created_by → users (null on delete)
- **Indexes**: [employee_id, date], [date, status], is_approved
- **Soft Deletes**: Yes

#### hr_leave_types
- **PK**: id
- **Columns**: name, code (unique, nullable), color (nullable), is_paid, requires_approval, max_consecutive_days (nullable), min_notice_days (nullable), allow_half_day, allow_negative_balance, is_active, created_by, custom_fields
- **FK**: created_by → users (null on delete)
- **Indexes**: is_active
- **Soft Deletes**: Yes

#### hr_leave_balances
- **PK**: id
- **Columns**: employee_id, leave_type_id, year, allocated, accrued, used, carried_over, remaining, created_by
- **FK**: employee_id → hr_employees (cascade delete), leave_type_id → hr_leave_types (cascade delete), created_by → users (null on delete)
- **Indexes**: [employee_id, leave_type_id, year]
- **Soft Deletes**: Yes

#### hr_leave_requests
- **PK**: id
- **Columns**: employee_id, leave_type_id, start_date, end_date, total_days, is_half_day, half_day_session (nullable), reason (nullable), status, approved_by (nullable), approved_at (nullable), rejection_reason (nullable), created_by, custom_fields
- **FK**: employee_id → hr_employees (cascade delete), leave_type_id → hr_leave_types (cascade delete), approved_by → users (null on delete), created_by → users (null on delete)
- **Indexes**: [employee_id, start_date], [status, leave_type_id], [start_date, end_date]
- **Soft Deletes**: Yes

#### hr_payrolls
- **PK**: id
- **Columns**: payroll_number (unique), employee_id, pay_period_start, pay_period_end, pay_date (nullable), status, basic_salary, overtime_pay (nullable), bonus (nullable), allowances (nullable), gross_pay, tax_deduction (nullable), social_security (nullable), health_insurance (nullable), other_deductions (nullable), total_deductions, net_pay, currency, notes (nullable), approved_by (nullable), approved_at (nullable), created_by, custom_fields
- **FK**: employee_id → hr_employees (cascade delete), approved_by → users (null on delete), created_by → users (null on delete)
- **Indexes**: [employee_id, pay_period_start], [status, pay_date], payroll_number
- **Soft Deletes**: Yes

---

### POS Module

#### pos_categories
- **PK**: id
- **Columns**: name, branch_id, created_by, brand_id
- **FK**: branch_id → branches, created_by → users, brand_id → brands

#### pos_sub_categories
- **PK**: id
- **Columns**: name, category_id, branch_id, created_by, brand_id
- **FK**: category_id → pos_categories, branch_id → branches, created_by → users, brand_id → brands

#### pos_products
- **PK**: id
- **Columns**: name, amount, amount_type, description, image, category_id, sub_category_id, branch_id, created_by, brand_id
- **FK**: category_id → pos_categories, sub_category_id → pos_sub_categories, branch_id → branches, created_by → users, brand_id → brands

#### pos_product_stocks
- **PK**: id
- **Columns**: product_id, branch_id, tag_id, quantity, created_by, brand_id
- **FK**: product_id → pos_products, branch_id → branches, tag_id → pos_tags, created_by → users, brand_id → brands

#### pos_offer_prices
- **PK**: id
- **Columns**: product_id, branch_id, amount, original_price, started_at, ended_at, created_by, brand_id
- **FK**: product_id → pos_products, branch_id → branches, created_by → users, brand_id → brands

#### pos_barcodes
- **PK**: id
- **Columns**: barcode_number, product_id, category_id, created_by, brand_id
- **FK**: product_id → pos_products, category_id → pos_categories, created_by → users, brand_id → brands

### Survey Module

#### survey_surveys
- **PK**: id
- **Columns**: title, description, status, settings, theme_id, template_id, default_locale, supported_locales, published_at, closed_at, created_by, brand_id
- **FK**: theme_id → survey_themes, template_id → survey_templates, created_by → users, brand_id → brands

#### survey_pages
- **PK**: id
- **Columns**: survey_id, title, description, order, settings
- **FK**: survey_id → survey_surveys (cascade delete)

#### survey_questions
- **PK**: id
- **Columns**: survey_id, page_id, type, title, description, help_text, is_required, order, config, validation, branching
- **FK**: survey_id → survey_surveys (cascade delete), page_id → survey_pages (cascade delete)

#### survey_responses
- **PK**: id
- **Columns**: survey_id, share_id, respondent_type, respondent_id, respondent_email, status, started_at, completed_at, score, brand_id
- **FK**: survey_id → survey_surveys (cascade delete), share_id → survey_shares, respondent_id → users, brand_id → brands

#### survey_answers
- **PK**: id
- **Columns**: response_id, question_id, value, selected_options, matrix_answers, rating_value, computed_score
- **FK**: response_id → survey_responses (cascade delete), question_id → survey_questions

#### survey_templates / survey_themes / survey_shares / survey_automation_rules / survey_webhooks
- **PK**: id
- **Note**: All Survey module tables follow `survey_` prefix convention and reference `survey_surveys` via `survey_id` where applicable.

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
- **FK**: contact_id → crm_contacts, company_id → crm_companies, opportunity_id → crm_opportunities, created_by → users, assigned_to → users
- **Indexes**: [status, quotation_date], [contact_id, status], [company_id, status], quotation_number
- **Soft Deletes**: Yes

#### orders
- **PK**: id
- **Columns**: order_number (unique), quotation_id, contact_id, company_id, order_date, delivery_date, status, payment_status, subtotal, tax_amount, discount_amount, shipping_amount, total_amount, currency, tax_rate, shipping_address, billing_address, notes, created_by, assigned_to, custom_fields
- **FK**: quotation_id → quotations, contact_id → crm_contacts, company_id → crm_companies, created_by → users, assigned_to → users
- **Indexes**: [status, order_date], [payment_status, order_date], [contact_id, status], [company_id, status], order_number
- **Soft Deletes**: Yes

#### invoices
- **PK**: id
- **Columns**: invoice_number (unique), order_id, contact_id, company_id, invoice_date, due_date, status, subtotal, tax_amount, discount_amount, total_amount, paid_amount, balance_amount, currency, tax_rate, notes, terms_conditions, created_by, assigned_to, custom_fields
- **FK**: order_id → orders, contact_id → crm_contacts, company_id → crm_companies, created_by → users, assigned_to → users
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

### Email Marketing Module

#### em_campaigns
- **PK**: id
- **Columns**: name, subject, template_id (nullable), credential_id (nullable), from_name, from_email, body_html (nullable), body_text (nullable), status, scheduled_at (nullable), sent_at (nullable), total_recipients, delivered_count, opened_count, clicked_count, bounced_count, unsubscribed_count, created_by, custom_fields
- **FK**: template_id → em_templates, credential_id → em_credentials, created_by → users
- **Indexes**: [status, scheduled_at], [credential_id, status], created_by
- **Soft Deletes**: Yes

#### em_templates
- **PK**: id
- **Columns**: name, subject, body_html, body_text (nullable), status, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: status, created_by
- **Soft Deletes**: Yes

#### em_contacts
- **PK**: id
- **Columns**: email (unique), first_name, last_name, phone (nullable), company (nullable), status, unsubscribed_at (nullable), created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: email, status, [status, created_at]
- **Soft Deletes**: Yes

#### em_contact_lists
- **PK**: id
- **Columns**: name, description (nullable), contacts_count, status, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: status, created_by
- **Soft Deletes**: Yes

#### em_contact_list_items
- **PK**: id
- **Columns**: contact_list_id, contact_id
- **FK**: contact_list_id → em_contact_lists (cascade delete), contact_id → em_contacts (cascade delete)
- **Indexes**: [contact_list_id, contact_id] (unique), contact_list_id, contact_id

#### em_credentials
- **PK**: id
- **Columns**: name, provider, account_sid (nullable), auth_token (nullable), from_email, from_name, is_default, status, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [provider, status], is_default, created_by
- **Soft Deletes**: Yes

#### em_automation_rules
- **PK**: id
- **Columns**: name, trigger_type, trigger_config (nullable), action_type, action_config (nullable), is_active, last_triggered_at (nullable), created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [trigger_type, is_active], is_active, created_by
- **Soft Deletes**: Yes

#### em_webhooks
- **PK**: id
- **Columns**: name, url, secret, events, is_active, last_triggered_at (nullable), created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: is_active, created_by
- **Soft Deletes**: Yes

#### em_ab_tests
- **PK**: id
- **Columns**: campaign_id, variant_name, subject (nullable), body_html (nullable), body_text (nullable), percentage, sent_count, opened_count, clicked_count, is_winner, created_by, custom_fields
- **FK**: campaign_id → em_campaigns (cascade delete), created_by → users
- **Indexes**: campaign_id, is_winner, [campaign_id, variant_name]
- **Soft Deletes**: Yes

#### em_import_jobs
- **PK**: id
- **Columns**: contact_list_id, file_path, status, total_rows, processed_rows, failed_rows, error_log (nullable), started_at (nullable), completed_at (nullable), created_by, custom_fields
- **FK**: contact_list_id → em_contact_lists, created_by → users
- **Indexes**: [status, created_at], contact_list_id, created_by
- **Soft Deletes**: Yes

#### em_sending_logs
- **PK**: id
- **Columns**: campaign_id, contact_id, status, provider_message_id (nullable), sent_at, delivered_at (nullable), opened_at (nullable), clicked_at (nullable), failed_reason (nullable), cost (nullable), created_by, custom_fields
- **FK**: campaign_id → em_campaigns, contact_id → em_contacts, created_by → users
- **Indexes**: [campaign_id, status], [contact_id, status], [status, sent_at], provider_message_id

#### em_unsubscribes
- **PK**: id
- **Columns**: contact_id, campaign_id (nullable), reason (nullable), ip_address (nullable), created_by
- **FK**: contact_id → em_contacts, campaign_id → em_campaigns, created_by → users
- **Indexes**: contact_id, [contact_id, campaign_id] (unique)

---

### SMS Marketing Module

#### sm_campaigns
- **PK**: id
- **Columns**: name, body, template_id (nullable), credential_id (nullable), status, scheduled_at (nullable), sent_at (nullable), total_recipients, delivered_count, failed_count, cost, created_by, custom_fields
- **FK**: template_id → sm_templates, credential_id → sm_credentials, created_by → users
- **Indexes**: [status, scheduled_at], [credential_id, status], created_by
- **Soft Deletes**: Yes

#### sm_templates
- **PK**: id
- **Columns**: name, body, status, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: status, created_by
- **Soft Deletes**: Yes

#### sm_contacts
- **PK**: id
- **Columns**: phone (unique), first_name (nullable), last_name (nullable), email (nullable), status, opted_out_at (nullable), created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: phone, status, [status, created_at]
- **Soft Deletes**: Yes

#### sm_contact_lists
- **PK**: id
- **Columns**: name, description (nullable), contacts_count, status, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: status, created_by
- **Soft Deletes**: Yes

#### sm_contact_list_items
- **PK**: id
- **Columns**: contact_list_id, contact_id
- **FK**: contact_list_id → sm_contact_lists (cascade delete), contact_id → sm_contacts (cascade delete)
- **Indexes**: [contact_list_id, contact_id] (unique), contact_list_id, contact_id

#### sm_credentials
- **PK**: id
- **Columns**: name, provider, account_sid (nullable), auth_token (nullable), from_number (nullable), is_default, status, created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [provider, status], is_default, created_by
- **Soft Deletes**: Yes

#### sm_automation_rules
- **PK**: id
- **Columns**: name, trigger_type, trigger_config (nullable), action_type, action_config (nullable), is_active, last_triggered_at (nullable), created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: [trigger_type, is_active], is_active, created_by
- **Soft Deletes**: Yes

#### sm_webhooks
- **PK**: id
- **Columns**: name, url, secret, events, is_active, last_triggered_at (nullable), created_by, custom_fields
- **FK**: created_by → users
- **Indexes**: is_active, created_by
- **Soft Deletes**: Yes

#### sm_ab_tests
- **PK**: id
- **Columns**: campaign_id, variant_name, body (nullable), percentage, sent_count, opened_count, is_winner, created_by, custom_fields
- **FK**: campaign_id → sm_campaigns (cascade delete), created_by → users
- **Indexes**: campaign_id, is_winner, [campaign_id, variant_name]
- **Soft Deletes**: Yes

#### sm_import_jobs
- **PK**: id
- **Columns**: contact_list_id, file_path, status, total_rows, processed_rows, failed_rows, error_log (nullable), started_at (nullable), completed_at (nullable), created_by, custom_fields
- **FK**: contact_list_id → sm_contact_lists, created_by → users
- **Indexes**: [status, created_at], contact_list_id, created_by
- **Soft Deletes**: Yes

#### sm_sending_logs
- **PK**: id
- **Columns**: campaign_id, contact_id, status, provider_message_id (nullable), sent_at, delivered_at (nullable), failed_reason (nullable), cost (nullable), created_by, custom_fields
- **FK**: campaign_id → sm_campaigns, contact_id → sm_contacts, created_by → users
- **Indexes**: [campaign_id, status], [contact_id, status], [status, sent_at], provider_message_id

#### sm_opt_outs
- **PK**: id
- **Columns**: contact_id, campaign_id (nullable), reason (nullable), created_by
- **FK**: contact_id → sm_contacts, campaign_id → sm_campaigns, created_by → users
- **Indexes**: contact_id, [contact_id, campaign_id] (unique)

---

## Database Migration Paths

### Tenant Migrations
- Main: `backend/database/migrations/tenant/`
- Modules: `backend/modules/*/database/migrations/tenant/`

## Cross-Database Relationships

- **branches.brand_id** → brands (landlord database)
