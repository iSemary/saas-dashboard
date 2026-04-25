# Survey Module — Full Implementation Plan (Enhanced)

Build a Typeform/SurveyMonkey-level Survey module using DDD + Strategy Pattern, integrating tightly with existing modules (Email, Notification, FileManager, CRM, Localization), with public-facing respondent UI, embeddable widget, quizzes/scoring, multi-language support, and AI-generation hooks.

---

## Domain Model

```
Survey ──┬── has many ── SurveyPage ── has many ── SurveyQuestion ── has many ── SurveyQuestionOption
         │                                                          └── has many ── SurveyQuestionTranslation
         ├── has many ── SurveyResponse ── has many ── SurveyAnswer
         ├── has many ── SurveyShare (distribution channels)
         ├── has many ── SurveyAutomationRule
         ├── has many ── SurveyWebhook
         ├── belongs to ── SurveyTemplate (optional source)
         └── belongs to ── SurveyTheme

SurveyTemplate ── pre-built blueprints (system + user-created)
SurveyTheme ── visual styling
```

---

## Database — 12 Tenant Tables

(Migrations dated `2026_04_25_*` for consistency.)

### 1. `surveys`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| title | string | |
| description | text nullable | |
| status | enum: draft, active, paused, closed, archived | default draft |
| settings | json | allow_multiple, show_progress, require_login, shuffle_questions, thank_you_message, close_date, is_scored, passing_score, show_score_after, show_correct_answers, require_captcha, single_question_mode |
| theme_id | FK survey_themes nullable | |
| template_id | FK survey_templates nullable | |
| default_locale | string default 'en' | |
| supported_locales | json nullable | |
| published_at, closed_at | timestamp nullable | |
| created_by | FK users | |
| soft_deletes + timestamps | | |

### 2. `survey_pages`
id, survey_id (cascade), title, description, order, settings json, timestamps

### 3. `survey_questions`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| survey_id | FK cascade | |
| page_id | FK cascade | |
| type | enum (20 types: text, textarea, email, number, phone, url, date, multiple_choice, checkbox, dropdown, rating, nps, likert_scale, matrix, slider, file_upload, image_choice, ranking, yes_no, signature) | |
| title, description, help_text | string/text | |
| is_required | boolean default false | |
| order | int | |
| config | json | per-type config |
| validation | json | min, max, pattern, min_length, max_length, min_select, max_select |
| branching | json | {logic: AND/OR, conditions: [{question_id, operator, value}], action: show/hide/skip_to_page/skip_to_question, target_id} |
| correct_answer | json nullable | for quiz mode |
| image_url | string nullable | |
| timestamps | | |

### 4. `survey_question_options`
id, question_id (cascade), label, value, order, image_url nullable, is_other bool, **point_value int default 0** (quiz scoring), timestamps

### 5. `survey_question_translations`
id, question_id (cascade), locale, title, description nullable, help_text nullable, options_translations json (`{option_id: label}`), unique(question_id, locale), timestamps

### 6. `survey_responses`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| survey_id | FK cascade | |
| share_id | FK survey_shares nullable | track which share generated it |
| respondent_type | enum: anonymous, authenticated, email | |
| respondent_id | FK users nullable | |
| respondent_email, respondent_name | string nullable | |
| status | enum: started, completed, partial, disqualified | |
| started_at, completed_at | timestamp nullable | |
| ip_address, user_agent | string/text nullable | |
| time_spent_seconds | int nullable | |
| score, max_score | int nullable | quiz mode |
| passed | bool nullable | quiz mode |
| resume_token | string unique nullable | for resume |
| locale | string default 'en' | |
| custom_fields | json nullable | |
| timestamps | | |

### 7. `survey_answers`
id, response_id (cascade), question_id, value text nullable, selected_options json nullable, file_id (FK FileManager files) nullable, matrix_answers json nullable, rating_value int nullable, computed_score int nullable, timestamps

### 8. `survey_templates`
id, name, description, category enum (customer_satisfaction, employee_engagement, market_research, product_feedback, event_feedback, nps, csat, ces, education, health, general, 360_feedback, course_evaluation), structure json, is_system bool default false, created_by nullable, soft_deletes + timestamps

### 9. `survey_themes`
id, name, colors json, font_family nullable, logo_url nullable, background_image_url nullable, button_style json nullable, is_system bool default false, created_by nullable, soft_deletes + timestamps

### 10. `survey_automation_rules`
id, survey_id (cascade), name, trigger_type enum, conditions json nullable, action_type enum (send_email, update_field, create_activity, send_notification, trigger_webhook, create_crm_activity), action_config json, is_active bool, created_by, soft_deletes + timestamps

### 11. `survey_webhooks`
id, survey_id (cascade), name, url, secret (HMAC-SHA256 signing), events json, is_active, last_triggered_at, created_by, soft_deletes + timestamps

### 12. `survey_shares`
id, survey_id (cascade), channel enum (email, link, embed, sms, qr_code, social), token unique, config json nullable, max_uses int nullable, uses_count int default 0, expires_at nullable, created_by, timestamps

---

## DDD Backend Architecture

### Domain Layer
**ValueObjects/** — `SurveyStatus`, `QuestionType` (with `isChoiceType`/`isRatingType`/`requiresOptions`), `RespondentType`, `ResponseStatus`, `SurveyCategory`, `ShareChannel`, `AutomationTrigger`, `AutomationAction`, `BranchingOperator` (eq, neq, contains, gt, lt, in, not_in)

**Strategies/** — pluggable behaviors:
- `QuestionType/` — Interface + `DefaultQuestionTypeStrategy` (validate answer per type)
- `Branching/` — Interface + `DefaultBranchingStrategy` (evaluate AND/OR conditions)
- `Scoring/` — Interface + `DefaultScoringStrategy` (compute quiz score)
- `Distribution/` — Interface + `EmailDistribution` (uses Email module), `LinkDistribution`, `EmbedDistribution`, `SmsDistribution`, `SocialDistribution`, `QrCodeDistribution`
- `Notification/` — Interface + `EmailNotification`, `SmsNotification`, `PushNotification` (uses Notification module)
- **`AiGeneration/`** — Interface + `OpenAiGenerationStrategy` (stub for AI-generated surveys/questions)
- **`Piping/`** — Interface + `DefaultPipingStrategy` (resolve `{{q1.value}}` placeholders)

**Entities/** (12 rich models with business methods + event dispatch):
`Survey`, `SurveyPage`, `SurveyQuestion`, `SurveyQuestionOption`, `SurveyQuestionTranslation`, `SurveyResponse`, `SurveyAnswer`, `SurveyTemplate`, `SurveyTheme`, `SurveyAutomationRule`, `SurveyWebhook`, `SurveyShare`

**Events/** — `SurveyCreated`, `SurveyPublished`, `SurveyClosed`, `SurveyResponseCreated`, `SurveyResponseCompleted`, `SurveyQuestionAnswered`

**Exceptions/** — `InvalidSurveyStatusTransition`, `InvalidQuestionTypeException`, `SurveyAlreadyPublishedException`, `SurveyClosedException`, `InvalidAnswerException`, `ShareExpiredException`, `SurveyNotPublishableException`

### Application Layer
**DTOs/** — Create/Update per entity + `SubmitSurveyResponseData`, `SubmitAnswerData`, `ReorderData`, `GenerateSurveyFromAiData`

**UseCases/** (30+ classes):
- `Survey/` — Create, Update, Delete, Publish, Pause, Close, Archive, Duplicate, GenerateFromAi
- `SurveyQuestion/` — Create, Update, Delete, Reorder, UpdateBranching, AddTranslation
- `SurveyPage/` — Create, Update, Delete, Reorder
- `SurveyResponse/` — Start, SubmitAnswer, Complete, Disqualify, Resume
- `SurveyTemplate/` — Create, Delete, CreateSurveyFromTemplate
- `SurveyTheme/` — Create, Update, Delete
- `AutomationRule/`, `Webhook/`, `Share/` — full CRUD + toggle/regenerate
- `Analytics/` — GetSurveyAnalytics, GetQuestionBreakdown, ExportResponses (CSV/Excel/JSON)

**Services/** — `QuestionPipingService` (resolve `{{q1.value}}`), `SurveyScoreCalculator`

### Infrastructure Layer
**Persistence/** — 12 repos (interface + Eloquent impl, `TableListTrait`)

**Jobs/** (Redis/Horizon):
- `ProcessSurveyResponseJob` — post-completion: scoring → automation → webhooks
- `SendSurveyEmailJob` — uses Email module's send infrastructure
- `ExportSurveyResponsesJob` — CSV/Excel/JSON export
- `TriggerSurveyWebhookJob` — async webhook with HMAC-SHA256 signature (`X-Survey-Signature` header)
- `BroadcastResponseToWebSocketJob` — real-time analytics feed

**Listeners/**:
- `TriggerAutomationOnResponseCompleted`
- `DispatchWebhookOnSurveyEvent`
- `CreateCrmActivityOnResponseCompleted` — cross-module: matches respondent_email to CRM Contact
- `BroadcastResponseUpdate` — live analytics

**Integrations/** — `EmailModuleIntegration`, `NotificationModuleIntegration`, `FileManagerIntegration`, `CrmIntegration`

### Presentation Layer
**Controllers/Api/** — pure DDD namespace `Modules\Survey\Presentation\Http\Controllers\Api\`:
- `SurveyApiController` (CRUD + publish/pause/close/duplicate/generate-from-ai/dashboard)
- `SurveyQuestionApiController`, `SurveyPageApiController`, `SurveyResponseApiController`
- **`SurveyPublicApiController`** (no auth, throttled) — render + start + answer + complete + resume
- `SurveyTemplateApiController`, `SurveyThemeApiController`
- `SurveyAutomationRuleApiController`, `SurveyWebhookApiController`, `SurveyShareApiController`
- `SurveyAnalyticsApiController` (analytics + export)
- `SurveyTranslationApiController` (multi-language management)

**Requests/** — 14+ form requests with full validation

**Routes/api.php** — three groups:
1. Authenticated `/tenant/survey/...` (auth:api, tenant_roles, throttle:60,1)
2. Public `/public/survey/{token}/...` (throttle:30,1, optional CAPTCHA)
3. Embed `/embed/survey/{token}` (returns iframe HTML)

---

## API Routes

### Authenticated (`/tenant/survey/...`)
- `GET /dashboard` — stats
- `GET|POST /surveys`, `GET|PUT|DELETE /surveys/{id}`
- `POST /surveys/{id}/publish|pause|close|duplicate`
- `POST /surveys/generate-from-ai` — AI generation stub
- `GET|POST /surveys/{sid}/pages`, `GET|PUT|DELETE /surveys/{sid}/pages/{id}`, `POST /surveys/{sid}/pages/reorder`
- `GET|POST /surveys/{sid}/questions`, `GET|PUT|DELETE /surveys/{sid}/questions/{id}`, `POST /surveys/{sid}/questions/reorder`, `PATCH /surveys/{sid}/questions/{id}/branching`
- `GET|POST /surveys/{sid}/questions/{qid}/translations`, `PUT|DELETE /surveys/{sid}/questions/{qid}/translations/{locale}`
- `GET /surveys/{sid}/responses`, `GET|DELETE /surveys/{sid}/responses/{id}`
- `GET|POST /templates`, `DELETE /templates/{id}`, `POST /templates/{id}/create-survey`
- `GET|POST /themes`, `GET|PUT|DELETE /themes/{id}`
- `GET|POST|PUT|DELETE /surveys/{sid}/automation-rules` (+ `POST /toggle`)
- `GET|POST|PUT|DELETE /surveys/{sid}/webhooks` (+ `POST /toggle`, `POST /regenerate-secret`)
- `GET|POST|DELETE /surveys/{sid}/shares`
- `GET /surveys/{sid}/analytics`, `/analytics/summary`, `/analytics/question/{qid}`, `/analytics/export?format=csv|excel|json`

### Public (`/public/survey/...`, throttle:30,1)
- `GET /{token}` — render survey (returns survey + pages + questions + theme + locale-resolved content)
- `POST /{token}/start` — create response, return resume_token
- `POST /{token}/answer` — submit single answer (auto-save)
- `POST /{token}/complete` — finalize response
- `GET /{token}/resume/{resumeToken}` — resume partial response

### Embed (`/embed/survey/{token}`) — returns HTML/iframe wrapper

---

## Frontend — Next.js

### Authenticated Pages: `tenant-frontend/src/app/dashboard/modules/survey/`
| Path | Type | Description |
|---|---|---|
| `page.tsx` | Custom dashboard | Stats + charts (surveys, responses, completion rate, NPS, real-time feed via WebSocket) |
| `surveys/page.tsx` | SimpleCRUDPage | Survey listing with status filter |
| `surveys/[id]/page.tsx` | **Custom Survey Builder** | Drag-drop (`@dnd-kit/core`), page manager, branching editor, translations editor, preview |
| `surveys/[id]/responses/page.tsx` | Custom table | Listing + detail expand + filters |
| `surveys/[id]/analytics/page.tsx` | Custom analytics | ApexCharts: completion rate, NPS, per-question, time spent, score distribution |
| `surveys/[id]/share/page.tsx` | Custom | Share dialog: link/email/embed/QR/SMS/social + share management |
| `templates/page.tsx` | SimpleCRUDPage | Template gallery (system + custom) |
| `themes/page.tsx` | SimpleCRUDPage | Theme management with color picker |
| `settings/page.tsx` | Custom | Automation rules + webhooks |

### Public Pages: `tenant-frontend/src/app/s/`
- `s/[token]/page.tsx` — **Public respondent UI** (mobile-first, themed, framer-motion transitions, single-question mode + multi-page mode toggle)
- `s/[token]/thanks/page.tsx` — Thank you / score reveal page

### Embed Widget
- `tenant-frontend/public/embed.js` — small bundled JS that injects `<iframe src="/s/{token}?embed=1">` into any external site

### Custom Components: `tenant-frontend/src/components/survey/`
- `survey-builder.tsx` — main builder shell
- `question-editor.tsx` — per-question type editor
- `question-type-picker.tsx` — add question panel
- `branching-editor.tsx` — visual logic editor (AND/OR + conditions + actions)
- `survey-preview.tsx` — live preview
- `survey-page-manager.tsx` — pages CRUD + reorder
- `translation-editor.tsx` — multi-language content editor
- `response-detail.tsx`, `analytics-charts.tsx`
- `share-dialog.tsx` — link/email/embed/QR/SMS/social
- `public-survey-renderer.tsx` — used by public route to render the survey
- `question-renderers/` — one component per question type (20 files)
- `qr-code.tsx` — uses `qrcode.react`

### API Client (`tenant-resources.ts`)
Add `const SVY = "/tenant/survey"` section + `const SVY_PUBLIC = "/public/survey"` for the public renderer.

---

## Pre-Seeded Data

**`SurveyTemplatesSeeder`** ships these system templates:
- NPS, CSAT, CES, Customer Satisfaction
- Employee Engagement, 360° Feedback
- Product Feedback, Event Feedback
- Course Evaluation, Health Check, Market Research

**`SurveyThemesSeeder`** ships:
- Default, Minimal, Dark, Vibrant, Corporate

**`SurveyPermissionSeeder`** — RBAC permissions:
`view.survey`, `create.survey`, `edit.survey`, `delete.survey`, `publish.survey`, `view.responses`, `export.responses`, `manage.templates`, `manage.themes`, `manage.automation`, `manage.webhooks`

---

## ModulesSeeder Update

```php
[
    'module_key' => 'survey',
    'name' => 'Survey',
    'description' => 'Create surveys, distribute via link/email/embed/QR, collect responses, analyze feedback',
    'route' => '/dashboard/modules/survey',
    'icon' => 'survey.png',
    'slogan' => 'Your opinion matters',
    'status' => 'active',
    'navigation' => [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => '/dashboard/modules/survey', 'icon' => 'LayoutDashboard'],
        ['key' => 'surveys', 'label' => 'Surveys', 'route' => '/dashboard/modules/survey/surveys', 'icon' => 'FileText'],
        ['key' => 'templates', 'label' => 'Templates', 'route' => '/dashboard/modules/survey/templates', 'icon' => 'LayoutTemplate'],
        ['key' => 'themes', 'label' => 'Themes', 'route' => '/dashboard/modules/survey/themes', 'icon' => 'Palette'],
        ['key' => 'settings', 'label' => 'Settings', 'route' => '/dashboard/modules/survey/settings', 'icon' => 'Settings'],
    ],
],
```

---

## Cross-Module Integrations

| Integration | Implementation |
|---|---|
| **Email module** | `EmailDistributionStrategy` + `send_email` automation action call Email module's send services using existing templates |
| **Notification module** | `send_notification` automation calls `NotificationService::send()`; real-time response feed broadcasts via existing WebSocket infra |
| **FileManager module** | `file_upload` question type stores via `MediaService`/`AWSService`; answers reference `file_id` |
| **CRM module** | `CreateCrmActivityOnResponseCompleted` listener: if respondent_email matches a CRM Contact, auto-create CRM Activity (type: survey_response) |
| **Localization module** | Multi-language surveys use existing `Localization` infrastructure for locale resolution |

---

## Build Phases

### Phase 1: DDD Foundation ✅ COMPLETE
1. ✅ Skeleton: `modules/Survey/` + `module.json` + `SurveyServiceProvider` + `SurveyEventServiceProvider` + `AGENTS.md`
2. ✅ ValueObjects (9 enums)
3. ✅ Strategies: 7 groups (QuestionType, Branching, Scoring, Distribution[6 impls], Notification[3 impls], AiGeneration, Piping)
4. ✅ Domain Exceptions (7)
5. ✅ Bind in ServiceProvider

### Phase 2: Data Model ✅ COMPLETE
6. ✅ 12 tenant migrations (`2026_04_25_*`)
7. ✅ 12 rich domain entities with business methods + event dispatch
8. ✅ 11 repository interfaces + Eloquent implementations (includes QuestionOption)
9. ✅ Bind repos in ServiceProvider

### Phase 3: Domain Events + Use Cases ✅ COMPLETE
10. ✅ Domain Events (6)
11. ✅ Listeners (6, including cross-module CRM listener + WebSocket)
12. ✅ DTOs (7)
13. ✅ UseCases (8)
14. ✅ Services: `QuestionPipingService`, `SurveyScoreCalculator`

### Phase 4: API Layer ✅ COMPLETE
15. ✅ Form Requests (5 core)
16. ✅ Controllers (13, including Public + QuestionOption)
17. ✅ Routes (authenticated + public + embed)

### Phase 5: Cross-Module Integrations ✅ STUBBED
18. ⏭️ `EmailModuleIntegration`, `NotificationModuleIntegration`, `FileManagerIntegration`, `CrmIntegration` (stubs ready)
19. ✅ Wire `EmailDistribution`/`NotificationDistribution` strategies
20. ✅ CRM activity listener stub

### Phase 6: Frontend (Authenticated) ✅ COMPLETE
21. ✅ `api-survey.ts` — Survey API client functions
22. ✅ Dashboard page (stats, quick links)
23. ✅ Surveys CRUD page (SimpleCRUDPage)
24. ⏭️ **Survey Builder** (drag-drop, branching, translations, preview) - Advanced feature deferred
25. ⏭️ Builder sub-components - Advanced feature deferred
26. ✅ Responses + Analytics pages (basic structure)
27. ✅ Share page (link/embed/QR/SMS/social)
28. ✅ Templates + Themes pages
29. ✅ Settings page (automation + webhooks)
30. ✅ Navigation update in dashboard layout

### Phase 7: Frontend (Public) ✅ COMPLETE
31. ✅ Public route `/s/[token]/page.tsx` (no auth, themed)
32. ✅ 12 question-renderer components (text, textarea, multiple_choice, checkbox, rating, nps, yes_no, dropdown, date, number, email, file_upload)
33. ✅ `public-survey-renderer.tsx` with framer-motion transitions
34. ✅ Single-question + multi-page mode toggle
35. ✅ Resume flow + auto-save (backend API ready)
36. ✅ Thank-you page with score reveal
37. ✅ **Embed JS widget** (`public/embed.js` + `/embed/survey/{token}` route)

### Phase 8: Seeders, Tests, Docs ✅ COMPLETE (Core)
38. ✅ `SurveyTemplateSeeder` (5 system templates)
39. ✅ `SurveyThemeSeeder` (4 system themes)
40. ⏭️ `SurveyPermissionSeeder` (RBAC) - needs RBAC module
41. ⏭️ Unit tests - Post-MVP
42. ⏭️ Feature tests - Post-MVP
43. ✅ Postman collection - `@/postman/Survey-Module.postman_collection.json`
44. ✅ ERD update - `@/docs/erd/survey-module-erd.md`
45. ✅ ModulesSeeder update + `AGENTS.md`

---

## Key Design Decisions

- **Question types as Strategy** — adding new types requires only a new strategy class
- **Branching as Strategy** — visual editor produces JSON spec that the strategy evaluates
- **Public + auth route separation** — public uses share token auth, fully unauthenticated
- **Multi-language via translations table** — keeps base questions clean; locale resolved at render
- **Quizzes via `point_value` + `correct_answer`** — opt-in via `survey.settings.is_scored`
- **Cross-module integration via dedicated Integration classes** — wraps Email/Notification/FileManager/CRM behind Survey-specific API to avoid leaking dependencies
- **Webhook signing** — HMAC-SHA256 with `X-Survey-Signature` header
- **Anti-spam** — `throttle:30,1` on public endpoints + per-survey CAPTCHA setting
- **Pure DDD namespace** — no legacy `Http\Controllers\Api`, only `Presentation\Http\Controllers\Api`
