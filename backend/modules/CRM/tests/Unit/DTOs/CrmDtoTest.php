<?php

namespace Modules\CRM\tests\Unit\DTOs;

use PHPUnit\Framework\TestCase;
use Modules\CRM\DTOs\CreateCompanyData;
use Modules\CRM\DTOs\UpdateCompanyData;
use Modules\CRM\DTOs\CreateContactData;
use Modules\CRM\DTOs\UpdateContactData;

class CrmDtoTest extends TestCase
{
    // ── CreateCompanyData ────────────────────────────────────────

    public function test_create_company_data_instantiation(): void
    {
        $dto = new CreateCompanyData(
            name: 'Acme Corp',
            email: 'info@acme.com',
            phone: '+1234567890',
            website: 'https://acme.com',
            industry: 'Technology',
            employee_count: 100,
            annual_revenue: 5000000.00,
            country: 'US',
            type: 'customer',
        );

        $this->assertSame('Acme Corp', $dto->name);
        $this->assertSame('info@acme.com', $dto->email);
        $this->assertSame(100, $dto->employee_count);
        $this->assertSame(5000000.00, $dto->annual_revenue);
    }

    public function test_create_company_data_to_array_includes_all(): void
    {
        $dto = new CreateCompanyData(
            name: 'Acme Corp',
            email: 'info@acme.com',
        );

        $array = $dto->toArray();
        $this->assertSame('Acme Corp', $array['name']);
        $this->assertSame('info@acme.com', $array['email']);
        $this->assertNull($array['phone']);
        $this->assertNull($array['website']);
    }

    public function test_create_company_data_minimal(): void
    {
        $dto = new CreateCompanyData(name: 'Minimal Inc');
        $this->assertSame('Minimal Inc', $dto->name);
        $this->assertNull($dto->email);
    }

    // ── UpdateCompanyData ────────────────────────────────────────

    public function test_update_company_data_filters_nulls(): void
    {
        $dto = new UpdateCompanyData(
            name: 'Updated Corp',
            country: 'UK',
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('country', $array);
        $this->assertArrayNotHasKey('email', $array);
        $this->assertArrayNotHasKey('phone', $array);
        $this->assertArrayNotHasKey('website', $array);
    }

    public function test_update_company_data_empty_returns_empty_array(): void
    {
        $dto = new UpdateCompanyData();
        $this->assertSame([], $dto->toArray());
    }

    // ── CreateContactData ────────────────────────────────────────

    public function test_create_contact_data_instantiation(): void
    {
        $dto = new CreateContactData(
            first_name: 'John',
            last_name: 'Doe',
            email: 'john@example.com',
            phone: '+1111111111',
            company_id: 5,
        );

        $this->assertSame('John', $dto->first_name);
        $this->assertSame('Doe', $dto->last_name);
        $this->assertSame(5, $dto->company_id);
    }

    public function test_create_contact_data_to_array_includes_all(): void
    {
        $dto = new CreateContactData(
            first_name: 'Jane',
            last_name: 'Smith',
        );

        $array = $dto->toArray();
        $this->assertSame('Jane', $array['first_name']);
        $this->assertSame('Smith', $array['last_name']);
        $this->assertNull($array['email']);
    }

    // ── UpdateContactData ────────────────────────────────────────

    public function test_update_contact_data_filters_nulls(): void
    {
        $dto = new UpdateContactData(
            first_name: 'Updated',
            email: 'new@example.com',
        );

        $array = $dto->toArray();
        $this->assertArrayHasKey('first_name', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayNotHasKey('last_name', $array);
        $this->assertArrayNotHasKey('phone', $array);
    }

    public function test_update_contact_data_empty_returns_empty_array(): void
    {
        $dto = new UpdateContactData();
        $this->assertSame([], $dto->toArray());
    }
}
