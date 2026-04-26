<?php

namespace Modules\EmailMarketing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\EmailMarketing\Domain\ValueObjects\EmCampaignStatus;
use Modules\EmailMarketing\Domain\ValueObjects\EmContactStatus;
use Modules\EmailMarketing\Domain\ValueObjects\EmLogStatus;
use Modules\EmailMarketing\Domain\ValueObjects\EmProviderType;

class EmailMarketingVoTest extends TestCase
{
    // ── EmCampaignStatus ──────────────────────────────────────────

    public function test_campaign_status_values(): void
    {
        $this->assertSame('draft', EmCampaignStatus::Draft->value);
        $this->assertSame('scheduled', EmCampaignStatus::Scheduled->value);
        $this->assertSame('sending', EmCampaignStatus::Sending->value);
        $this->assertSame('sent', EmCampaignStatus::Sent->value);
        $this->assertSame('paused', EmCampaignStatus::Paused->value);
        $this->assertSame('cancelled', EmCampaignStatus::Cancelled->value);
    }

    public function test_campaign_status_all_cases(): void
    {
        $this->assertCount(6, EmCampaignStatus::cases());
    }

    public function test_campaign_status_label(): void
    {
        $this->assertSame('Draft', EmCampaignStatus::Draft->label());
        $this->assertSame('Sending', EmCampaignStatus::Sending->label());
    }

    public function test_campaign_status_transitions_from_draft(): void
    {
        $this->assertTrue(EmCampaignStatus::Draft->canTransitionTo(EmCampaignStatus::Scheduled));
        $this->assertTrue(EmCampaignStatus::Draft->canTransitionTo(EmCampaignStatus::Sending));
        $this->assertTrue(EmCampaignStatus::Draft->canTransitionTo(EmCampaignStatus::Cancelled));
        $this->assertFalse(EmCampaignStatus::Draft->canTransitionTo(EmCampaignStatus::Sent));
    }

    public function test_campaign_status_transitions_from_sent(): void
    {
        $this->assertFalse(EmCampaignStatus::Sent->canTransitionTo(EmCampaignStatus::Draft));
        $this->assertFalse(EmCampaignStatus::Sent->canTransitionTo(EmCampaignStatus::Scheduled));
    }

    public function test_campaign_status_transitions_from_paused(): void
    {
        $this->assertTrue(EmCampaignStatus::Paused->canTransitionTo(EmCampaignStatus::Sending));
        $this->assertTrue(EmCampaignStatus::Paused->canTransitionTo(EmCampaignStatus::Cancelled));
        $this->assertFalse(EmCampaignStatus::Paused->canTransitionTo(EmCampaignStatus::Draft));
    }

    // ── EmContactStatus ──────────────────────────────────────────

    public function test_contact_status_values(): void
    {
        $this->assertSame('active', EmContactStatus::Active->value);
        $this->assertSame('unsubscribed', EmContactStatus::Unsubscribed->value);
        $this->assertSame('bounced', EmContactStatus::Bounced->value);
        $this->assertSame('complained', EmContactStatus::Complained->value);
    }

    public function test_contact_status_is_reachable(): void
    {
        $this->assertTrue(EmContactStatus::Active->isReachable());
        $this->assertFalse(EmContactStatus::Unsubscribed->isReachable());
        $this->assertFalse(EmContactStatus::Bounced->isReachable());
    }

    // ── EmLogStatus ─────────────────────────────────────────────

    public function test_log_status_values(): void
    {
        $this->assertSame('queued', EmLogStatus::Queued->value);
        $this->assertSame('sent', EmLogStatus::Sent->value);
        $this->assertSame('delivered', EmLogStatus::Delivered->value);
        $this->assertSame('opened', EmLogStatus::Opened->value);
        $this->assertSame('clicked', EmLogStatus::Clicked->value);
        $this->assertSame('bounced', EmLogStatus::Bounced->value);
        $this->assertSame('failed', EmLogStatus::Failed->value);
        $this->assertSame('unsubscribed', EmLogStatus::Unsubscribed->value);
    }

    public function test_log_status_all_cases(): void
    {
        $this->assertCount(8, EmLogStatus::cases());
    }

    // ── EmProviderType ──────────────────────────────────────────

    public function test_provider_type_values(): void
    {
        $this->assertSame('smtp', EmProviderType::Smtp->value);
        $this->assertSame('ses', EmProviderType::Ses->value);
        $this->assertSame('mailgun', EmProviderType::Mailgun->value);
        $this->assertSame('sendgrid', EmProviderType::Sendgrid->value);
        $this->assertSame('postmark', EmProviderType::Postmark->value);
    }

    public function test_provider_type_labels(): void
    {
        $this->assertSame('SMTP', EmProviderType::Smtp->label());
        $this->assertSame('Amazon SES', EmProviderType::Ses->label());
        $this->assertSame('Mailgun', EmProviderType::Mailgun->label());
        $this->assertSame('SendGrid', EmProviderType::Sendgrid->label());
        $this->assertSame('Postmark', EmProviderType::Postmark->label());
    }
}
