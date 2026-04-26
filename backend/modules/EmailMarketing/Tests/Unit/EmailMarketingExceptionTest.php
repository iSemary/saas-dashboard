<?php

namespace Modules\EmailMarketing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\EmailMarketing\Domain\Exceptions\InvalidEmCampaignTransition;
use Modules\EmailMarketing\Domain\Exceptions\EmContactAlreadyUnsubscribed;
use Modules\EmailMarketing\Domain\Exceptions\EmCredentialNotConfigured;
use Modules\EmailMarketing\Domain\ValueObjects\EmCampaignStatus;

class EmailMarketingExceptionTest extends TestCase
{
    // ── InvalidEmCampaignTransition ───────────────────────────────

    public function test_invalid_campaign_transition_message(): void
    {
        $exception = InvalidEmCampaignTransition::from(EmCampaignStatus::Sent, EmCampaignStatus::Draft);
        $this->assertSame("Cannot transition email campaign from [sent] to [draft].", $exception->getMessage());
    }

    public function test_invalid_campaign_transition_is_domain_exception(): void
    {
        $exception = InvalidEmCampaignTransition::from(EmCampaignStatus::Draft, EmCampaignStatus::Sent);
        $this->assertInstanceOf(\DomainException::class, $exception);
    }

    // ── EmContactAlreadyUnsubscribed ──────────────────────────────

    public function test_contact_already_unsubscribed_message(): void
    {
        $exception = EmContactAlreadyUnsubscribed::forEmail('user@example.com');
        $this->assertSame("Contact [user@example.com] is already unsubscribed.", $exception->getMessage());
    }

    public function test_contact_already_unsubscribed_is_domain_exception(): void
    {
        $exception = EmContactAlreadyUnsubscribed::forEmail('test@test.com');
        $this->assertInstanceOf(\DomainException::class, $exception);
    }

    // ── EmCredentialNotConfigured ──────────────────────────────────

    public function test_credential_not_configured_message(): void
    {
        $exception = EmCredentialNotConfigured::noDefault();
        $this->assertSame('No default email credential is configured for this tenant.', $exception->getMessage());
    }
}
