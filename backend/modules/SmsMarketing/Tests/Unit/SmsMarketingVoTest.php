<?php

namespace Modules\SmsMarketing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\SmsMarketing\Domain\ValueObjects\SmCampaignStatus;
use Modules\SmsMarketing\Domain\ValueObjects\SmContactStatus;
use Modules\SmsMarketing\Domain\ValueObjects\SmLogStatus;
use Modules\SmsMarketing\Domain\ValueObjects\SmProviderType;

class SmsMarketingVoTest extends TestCase
{
    // ── SmCampaignStatus ──────────────────────────────────────────

    public function test_campaign_status_values(): void
    {
        $this->assertSame('draft', SmCampaignStatus::Draft->value);
        $this->assertSame('scheduled', SmCampaignStatus::Scheduled->value);
        $this->assertSame('sending', SmCampaignStatus::Sending->value);
        $this->assertSame('sent', SmCampaignStatus::Sent->value);
        $this->assertSame('paused', SmCampaignStatus::Paused->value);
        $this->assertSame('cancelled', SmCampaignStatus::Cancelled->value);
    }

    public function test_campaign_status_all_cases(): void
    {
        $this->assertCount(6, SmCampaignStatus::cases());
    }

    public function test_campaign_status_label(): void
    {
        $this->assertSame('Draft', SmCampaignStatus::Draft->label());
        $this->assertSame('Sending', SmCampaignStatus::Sending->label());
    }

    public function test_campaign_status_transitions_from_draft(): void
    {
        $this->assertTrue(SmCampaignStatus::Draft->canTransitionTo(SmCampaignStatus::Scheduled));
        $this->assertTrue(SmCampaignStatus::Draft->canTransitionTo(SmCampaignStatus::Sending));
        $this->assertTrue(SmCampaignStatus::Draft->canTransitionTo(SmCampaignStatus::Cancelled));
        $this->assertFalse(SmCampaignStatus::Draft->canTransitionTo(SmCampaignStatus::Sent));
    }

    public function test_campaign_status_transitions_from_sent(): void
    {
        $this->assertFalse(SmCampaignStatus::Sent->canTransitionTo(SmCampaignStatus::Draft));
        $this->assertFalse(SmCampaignStatus::Sent->canTransitionTo(SmCampaignStatus::Scheduled));
    }

    public function test_campaign_status_transitions_from_paused(): void
    {
        $this->assertTrue(SmCampaignStatus::Paused->canTransitionTo(SmCampaignStatus::Sending));
        $this->assertTrue(SmCampaignStatus::Paused->canTransitionTo(SmCampaignStatus::Cancelled));
        $this->assertFalse(SmCampaignStatus::Paused->canTransitionTo(SmCampaignStatus::Draft));
    }

    // ── SmContactStatus ──────────────────────────────────────────

    public function test_contact_status_values(): void
    {
        $this->assertSame('active', SmContactStatus::Active->value);
        $this->assertSame('opted_out', SmContactStatus::OptedOut->value);
        $this->assertSame('invalid', SmContactStatus::Invalid->value);
    }

    public function test_contact_status_is_reachable(): void
    {
        $this->assertTrue(SmContactStatus::Active->isReachable());
        $this->assertFalse(SmContactStatus::OptedOut->isReachable());
        $this->assertFalse(SmContactStatus::Invalid->isReachable());
    }

    // ── SmLogStatus ─────────────────────────────────────────────

    public function test_log_status_values(): void
    {
        $this->assertSame('queued', SmLogStatus::Queued->value);
        $this->assertSame('sent', SmLogStatus::Sent->value);
        $this->assertSame('delivered', SmLogStatus::Delivered->value);
        $this->assertSame('failed', SmLogStatus::Failed->value);
    }

    public function test_log_status_all_cases(): void
    {
        $this->assertCount(4, SmLogStatus::cases());
    }

    // ── SmProviderType ──────────────────────────────────────────

    public function test_provider_type_values(): void
    {
        $this->assertSame('twilio', SmProviderType::Twilio->value);
        $this->assertSame('vonage', SmProviderType::Vonage->value);
        $this->assertSame('messagebird', SmProviderType::MessageBird->value);
        $this->assertSame('mock', SmProviderType::Mock->value);
    }

    public function test_provider_type_labels(): void
    {
        $this->assertSame('Twilio', SmProviderType::Twilio->label());
        $this->assertSame('Vonage', SmProviderType::Vonage->label());
        $this->assertSame('MessageBird', SmProviderType::MessageBird->label());
        $this->assertSame('Mock (Log only)', SmProviderType::Mock->label());
    }
}
