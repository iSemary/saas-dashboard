<?php

namespace Modules\SmsMarketing\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\SmsMarketing\Application\DTOs\Campaign\CreateSmCampaignDTO;

class SmsMarketingDtoTest extends TestCase
{
    public function test_create_sm_campaign_dto_construction(): void
    {
        $dto = new CreateSmCampaignDTO(
            name: 'Flash Sale SMS',
            template_id: 1,
            credential_id: 2,
            body: 'Get 50% off today only!',
            status: 'draft',
            scheduled_at: null,
            ab_test_id: null,
            settings: ['shorten_urls' => true],
            contact_list_ids: [1, 2],
        );

        $this->assertSame('Flash Sale SMS', $dto->name);
        $this->assertSame('Get 50% off today only!', $dto->body);
        $this->assertSame('draft', $dto->status);
        $this->assertSame([1, 2], $dto->contact_list_ids);
    }

    public function test_create_sm_campaign_dto_defaults(): void
    {
        $dto = new CreateSmCampaignDTO(name: 'Minimal');

        $this->assertSame('Minimal', $dto->name);
        $this->assertNull($dto->template_id);
        $this->assertNull($dto->body);
        $this->assertSame('draft', $dto->status);
        $this->assertNull($dto->contact_list_ids);
    }

    public function test_create_sm_campaign_dto_to_array_excludes_nulls_and_contact_list_ids(): void
    {
        $dto = new CreateSmCampaignDTO(
            name: 'Test',
            body: 'Hello {{name}}',
            contact_list_ids: [3],
        );

        $arr = $dto->toArray();

        $this->assertSame('Test', $arr['name']);
        $this->assertSame('Hello {{name}}', $arr['body']);
        $this->assertArrayNotHasKey('contact_list_ids', $arr);
        $this->assertArrayNotHasKey('template_id', $arr);
    }

    public function test_create_sm_campaign_dto_to_array_includes_non_null_optional_fields(): void
    {
        $dto = new CreateSmCampaignDTO(
            name: 'Scheduled',
            template_id: 10,
            credential_id: 5,
            body: 'Reminder!',
            status: 'scheduled',
            scheduled_at: '2026-07-01 10:00:00',
        );

        $arr = $dto->toArray();

        $this->assertSame(10, $arr['template_id']);
        $this->assertSame(5, $arr['credential_id']);
        $this->assertSame('scheduled', $arr['status']);
        $this->assertSame('2026-07-01 10:00:00', $arr['scheduled_at']);
    }
}
