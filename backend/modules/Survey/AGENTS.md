# Survey Module — Developer Guide

## Overview
Full-featured Survey module (Typeform/SurveyMonkey level) using DDD + Strategy Pattern with cross-module integrations.

## Architecture

```
Domain/          Pure business logic
  Entities/      Survey, SurveyPage, SurveyQuestion, SurveyResponse, etc.
  ValueObjects/  SurveyStatus, QuestionType, RespondentType, etc.
  Events/        SurveyCreated, SurveyResponseCompleted, etc.
  Strategies/    QuestionType, Branching, Scoring, Distribution, Notification, AiGeneration, Piping
  Exceptions/    Domain-specific exceptions

Application/
  DTOs/          CreateSurveyData, UpdateSurveyQuestionData, etc.
  UseCases/      CreateSurvey, PublishSurvey, SubmitResponse, etc.
  Services/      QuestionPipingService, SurveyScoreCalculator

Infrastructure/
  Persistence/   Repository implementations
  Jobs/          ProcessSurveyResponseJob, SendSurveyEmailJob, etc.
  Listeners/     TriggerAutomationOnResponseCompleted, etc.
  Integrations/  EmailModuleIntegration, NotificationModuleIntegration, etc.

Presentation/
  Http/
    Controllers/Api/   All API controllers (authenticated + public)
    Requests/          Form requests
  Routes/api.php       Authenticated + public + embed routes
```

## Route Groups

1. **Authenticated** (`/tenant/survey/...`) — All management APIs
2. **Public** (`/public/survey/{token}/...`) — No auth, for respondents
3. **Embed** (`/embed/survey/{token}`) — Returns iframe HTML

## Cross-Module Integrations

| Module | Integration |
|--------|-------------|
| Email | `EmailDistributionStrategy` uses Email module sending |
| Notification | `PushNotificationStrategy` uses Notification module |
| FileManager | `file_upload` type stores via MediaService |
| CRM | `CreateCrmActivityOnResponseCompleted` creates CRM Activities |
| Localization | Multi-language surveys use existing locale infra |

## Key Features

- 20 question types (text, email, number, choice, rating, NPS, matrix, etc.)
- Drag-drop survey builder
- Branching/skip logic (AND/OR conditions)
- Quizzes with scoring
- Multi-language support
- AI generation stubs
- Real-time analytics via WebSocket
- Embeddable widget

## Public Survey Flow

1. `GET /public/survey/{token}` — Render survey
2. `POST /public/survey/{token}/start` — Create response, get resume_token
3. `POST /public/survey/{token}/answer` — Submit answer (auto-save)
4. `POST /public/survey/{token}/complete` — Finalize

## Webhook Signing

All webhooks include `X-Survey-Signature` header with HMAC-SHA256 signature.
