<?php

namespace Modules\CRM\tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\CRM\Application\DTOs\CreateLeadDTO;

class CrmDtoTest extends TestCase
{
    public function test_create_lead_dto_construction(): void
    {
        $dto = new CreateLeadDTO(
            name: 'John Doe',
            email: 'john@example.com',
            phone: '+1234567890',
            company: 'Acme Inc',
            title: 'CEO',
            description: 'Test lead',
            status: 'new',
            source: 'website',
            expectedRevenue: 5000.0,
            expectedCloseDate: '2026-12-31',
            assignedTo: 1,
        );

        $this->assertSame('John Doe', $dto->name);
        $this->assertSame('john@example.com', $dto->email);
        $this->assertSame('+1234567890', $dto->phone);
        $this->assertSame('Acme Inc', $dto->company);
        $this->assertSame(5000.0, $dto->expectedRevenue);
    }

    public function test_create_lead_dto_defaults(): void
    {
        $dto = new CreateLeadDTO(name: 'Jane');

        $this->assertSame('Jane', $dto->name);
        $this->assertNull($dto->email);
        $this->assertNull($dto->phone);
        $this->assertSame('new', $dto->status);
        $this->assertNull($dto->source);
        $this->assertNull($dto->expectedRevenue);
    }

    public function test_create_lead_dto_from_array(): void
    {
        $dto = CreateLeadDTO::fromArray([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '+9876543210',
            'company' => 'Widget Co',
            'expected_revenue' => 10000.0,
            'expected_close_date' => '2026-06-30',
        ]);

        $this->assertSame('Jane Smith', $dto->name);
        $this->assertSame('jane@example.com', $dto->email);
        $this->assertSame(10000.0, $dto->expectedRevenue);
        $this->assertSame('2026-06-30', $dto->expectedCloseDate);
    }

    public function test_create_lead_dto_to_array(): void
    {
        $dto = new CreateLeadDTO(
            name: 'Test',
            email: 'test@example.com',
            status: 'contacted',
            source: 'referral',
        );

        $arr = $dto->toArray();

        $this->assertSame('Test', $arr['name']);
        $this->assertSame('test@example.com', $arr['email']);
        $this->assertSame('contacted', $arr['status']);
        $this->assertSame('referral', $arr['source']);
        $this->assertNull($arr['phone']);
        $this->assertNull($arr['expected_revenue']);
    }

    public function test_create_lead_dto_roundtrip(): void
    {
        $original = [
            'name' => 'Round Trip',
            'email' => 'rt@example.com',
            'phone' => '+111',
            'company' => 'RT Corp',
            'title' => 'Manager',
            'description' => 'Desc',
            'status' => 'qualified',
            'source' => 'partner',
            'expected_revenue' => 25000.0,
            'expected_close_date' => '2026-09-01',
            'assigned_to' => 5,
        ];

        $dto = CreateLeadDTO::fromArray($original);
        $result = $dto->toArray();

        $this->assertSame($original['name'], $result['name']);
        $this->assertSame($original['email'], $result['email']);
        $this->assertSame($original['status'], $result['status']);
        $this->assertSame($original['expected_revenue'], $result['expected_revenue']);
    }
}
