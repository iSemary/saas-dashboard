<?php

namespace Modules\EmailMarketing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\EmailMarketing\Application\DTOs\Campaign\CreateEmCampaignDTO;

class EmailMarketingDtoTest extends TestCase
{
    public function test_create_em_campaign_dto_construction(): void
    {
        $dto = new CreateEmCampaignDTO(
            name: 'Welcome Campaign',
            subject: 'Welcome to our service',
            template_id: 1,
            credential_id: 2,
            from_name: 'Acme Inc',
            from_email: 'noreply@acme.com',
            body_html: '<h1>Welcome!</h1>',
            body_text: 'Welcome!',
            status: 'draft',
            scheduled_at: null,
            ab_test_id: null,
            settings: ['track_opens' => true],
            contact_list_ids: [1, 2, 3],
        );

        $this->assertSame('Welcome Campaign', $dto->name);
        $this->assertSame('Welcome to our service', $dto->subject);
        $this->assertSame(1, $dto->template_id);
        $this->assertSame('draft', $dto->status);
        $this->assertSame([1, 2, 3], $dto->contact_list_ids);
    }

    public function test_create_em_campaign_dto_defaults(): void
    {
        $dto = new CreateEmCampaignDTO(
            name: 'Minimal Campaign',
            subject: 'Test',
        );

        $this->assertSame('Minimal Campaign', $dto->name);
        $this->assertSame('Test', $dto->subject);
        $this->assertNull($dto->template_id);
        $this->assertNull($dto->credential_id);
        $this->assertSame('draft', $dto->status);
        $this->assertNull($dto->contact_list_ids);
    }

    public function test_create_em_campaign_dto_to_array_excludes_nulls_and_contact_list_ids(): void
    {
        $dto = new CreateEmCampaignDTO(
            name: 'Test',
            subject: 'Sub',
            template_id: 5,
            contact_list_ids: [1, 2],
        );

        $arr = $dto->toArray();

        $this->assertSame('Test', $arr['name']);
        $this->assertSame('Sub', $arr['subject']);
        $this->assertSame(5, $arr['template_id']);
        $this->assertArrayNotHasKey('contact_list_ids', $arr);
        $this->assertArrayNotHasKey('credential_id', $arr);
        $this->assertArrayNotHasKey('scheduled_at', $arr);
    }

    public function test_create_em_campaign_dto_to_array_includes_non_null_optional_fields(): void
    {
        $dto = new CreateEmCampaignDTO(
            name: 'Full',
            subject: 'Full Sub',
            from_name: 'Sender',
            from_email: 'sender@test.com',
            body_html: '<p>Hi</p>',
            status: 'scheduled',
            scheduled_at: '2026-06-01 09:00:00',
        );

        $arr = $dto->toArray();

        $this->assertSame('Sender', $arr['from_name']);
        $this->assertSame('sender@test.com', $arr['from_email']);
        $this->assertSame('<p>Hi</p>', $arr['body_html']);
        $this->assertSame('scheduled', $arr['status']);
        $this->assertSame('2026-06-01 09:00:00', $arr['scheduled_at']);
    }
}
