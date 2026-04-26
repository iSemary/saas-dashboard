<?php

namespace Modules\SmsMarketing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\SmsMarketing\Domain\Exceptions\InvalidSmCampaignTransition;
use Modules\SmsMarketing\Domain\Exceptions\SmContactAlreadyOptedOut;
use Modules\SmsMarketing\Domain\Exceptions\SmCredentialNotConfigured;
use Modules\SmsMarketing\Domain\ValueObjects\SmCampaignStatus;

class SmsMarketingExceptionTest extends TestCase
{
    // ── InvalidSmCampaignTransition ───────────────────────────────

    public function test_invalid_campaign_transition_message(): void
    {
        $exception = InvalidSmCampaignTransition::from(SmCampaignStatus::Sent, SmCampaignStatus::Draft);
        $this->assertSame("Cannot transition SMS campaign from [sent] to [draft].", $exception->getMessage());
    }

    public function test_invalid_campaign_transition_is_domain_exception(): void
    {
        $exception = InvalidSmCampaignTransition::from(SmCampaignStatus::Draft, SmCampaignStatus::Sent);
        $this->assertInstanceOf(\DomainException::class, $exception);
    }

    // ── SmContactAlreadyOptedOut ──────────────────────────────────

    public function test_contact_already_opted_out_message(): void
    {
        $exception = SmContactAlreadyOptedOut::forPhone('+1234567890');
        $this->assertSame("Contact [+1234567890] has already opted out.", $exception->getMessage());
    }

    public function test_contact_already_opted_out_is_domain_exception(): void
    {
        $exception = SmContactAlreadyOptedOut::forPhone('+1111111111');
        $this->assertInstanceOf(\DomainException::class, $exception);
    }

    // ── SmCredentialNotConfigured ──────────────────────────────────

    public function test_credential_not_configured_message(): void
    {
        $exception = SmCredentialNotConfigured::noDefault();
        $this->assertSame('No default SMS credential is configured for this tenant.', $exception->getMessage());
    }
}
