<?php

declare(strict_types=1);

namespace Modules\Survey\Application\DTOs;

class SubmitResponseData
{
    public function __construct(
        public int $surveyId,
        public ?string $shareToken = null,
        public string $respondentType = 'anonymous',
        public ?int $respondentId = null,
        public ?string $respondentEmail = null,
        public ?string $respondentName = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
        public ?string $locale = null,
        public ?array $customFields = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            surveyId: $data['survey_id'],
            shareToken: $data['share_token'] ?? null,
            respondentType: $data['respondent_type'] ?? 'anonymous',
            respondentId: $data['respondent_id'] ?? null,
            respondentEmail: $data['respondent_email'] ?? null,
            respondentName: $data['respondent_name'] ?? null,
            ipAddress: $data['ip_address'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            locale: $data['locale'] ?? null,
            customFields: $data['custom_fields'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'survey_id' => $this->surveyId,
            'share_id' => null, // Will be resolved from token
            'respondent_type' => $this->respondentType,
            'respondent_id' => $this->respondentId,
            'respondent_email' => $this->respondentEmail,
            'respondent_name' => $this->respondentName,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'locale' => $this->locale ?? 'en',
            'custom_fields' => $this->customFields,
        ];
    }
}
