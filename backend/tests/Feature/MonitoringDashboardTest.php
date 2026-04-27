<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Modules\Tenant\Entities\Tenant;

class MonitoringDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user with monitoring permissions
        $this->user = User::factory()->create();
        // Assign appropriate roles/permissions for monitoring access
    }

    /** @test */
    public function monitoring_dashboard_loads_successfully()
    {
        $this->actingAs($this->user);

        $response = $this->get('/landlord/monitoring/');

        $response->assertStatus(200);
        $response->assertSee('Monitoring Dashboard');
        $response->assertSee('System Health');
        $response->assertSee('Tenant Behavior');
        $response->assertSee('Error Management');
        $response->assertSee('Resource Insights');
    }

    /** @test */
    public function system_health_page_loads_successfully()
    {
        $this->actingAs($this->user);

        $response = $this->get('/landlord/monitoring/system-health');

        $response->assertStatus(200);
        $response->assertSee('System Health Monitoring');
        $response->assertSee('System Uptime');
        $response->assertSee('Database Health');
        $response->assertSee('Queue Status');
    }

    /** @test */
    public function system_health_api_returns_json_data()
    {
        $this->actingAs($this->user);

        $response = $this->get('/landlord/monitoring/api/system-health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'timestamp',
            'system_load',
            'memory_usage',
            'active_connections'
        ]);
    }

    /** @test */
    public function tenant_behavior_api_returns_data()
    {
        $this->actingAs($this->user);

        $response = $this->get('/landlord/monitoring/api/tenant-behavior');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'timestamp',
            'active_sessions',
            'current_logins',
            'api_requests_per_minute'
        ]);
    }

    /** @test */
    public function admin_tools_consistency_check_works()
    {
        $this->actingAs($this->user);

        $response = $this->post('/landlord/monitoring/admin-tools/consistency-check');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'checks_performed',
            'issues_found',
            'recommendations'
        ]);
    }

    /** @test */
    public function tenant_database_operations_work()
    {
        $this->actingAs($this->user);

        // Create a test tenant
        $tenant = Tenant::factory()->create([
            'name' => 'test_tenant',
            'database' => 'test_tenant_db'
        ]);

        // Test database health endpoint
        $response = $this->get("/landlord/tenants/{$tenant->id}/health");
        $response->assertStatus(200);

        // Test re-migrate endpoint (would need proper setup in real scenario)
        $response = $this->post("/landlord/tenants/{$tenant->id}/remigrate");
        $response->assertStatus(200);

        // Test seed endpoint
        $response = $this->post("/landlord/tenants/{$tenant->id}/seed");
        $response->assertStatus(200);
    }

    /** @test */
    public function monitoring_requires_authentication()
    {
        // Test without authentication
        $response = $this->get('/landlord/monitoring/');
        $response->assertRedirect('/login');

        $response = $this->get('/landlord/monitoring/system-health');
        $response->assertRedirect('/login');

        $response = $this->get('/landlord/monitoring/api/system-health');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function tenant_specific_monitoring_loads()
    {
        $this->actingAs($this->user);

        $tenant = Tenant::factory()->create();

        $response = $this->get("/landlord/monitoring/tenant/{$tenant->id}");

        $response->assertStatus(200);
        $response->assertSee("Monitoring - {$tenant->name}");
    }
}
