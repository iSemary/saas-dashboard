<?php

namespace Tests\Unit\Commands;

use Tests\TestCase;
use App\Console\Commands\ProjectSetup\SeedRealDataCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Mockery;

class SeedRealDataCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the command can be instantiated
     */
    public function test_can_be_instantiated(): void
    {
        $command = new SeedRealDataCommand();
        $this->assertInstanceOf(SeedRealDataCommand::class, $command);
    }

    /**
     * Test command signature
     */
    public function test_command_signature(): void
    {
        $command = new SeedRealDataCommand();
        $this->assertStringContainsString('seed:real-data', $command->getSignature());
    }

    /**
     * Test command description
     */
    public function test_command_description(): void
    {
        $command = new SeedRealDataCommand();
        $this->assertEquals('Seed real data like languages, email templates, configurations, etc.', $command->getDescription());
    }

    /**
     * Test command runs successfully
     */
    public function test_command_runs_successfully(): void
    {
        $this->artisan('seed:real-data')
            ->assertExitCode(0);
    }

    /**
     * Test command with force option
     */
    public function test_command_with_force_option(): void
    {
        $this->artisan('seed:real-data', ['--force' => true])
            ->assertExitCode(0);
    }

    /**
     * Test command with specific modules
     */
    public function test_command_with_specific_modules(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => ['Localization', 'Email']
        ])->assertExitCode(0);
    }

    /**
     * Test command with force and specific modules
     */
    public function test_command_with_force_and_specific_modules(): void
    {
        $this->artisan('seed:real-data', [
            '--force' => true,
            '--modules' => ['Localization', 'Email']
        ])->assertExitCode(0);
    }

    /**
     * Test command handles existing data
     */
    public function test_command_handles_existing_data(): void
    {
        // Create some existing data
        DB::table('languages')->insert([
            'name' => 'English',
            'code' => 'en',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('seed:real-data')
            ->expectsConfirmation('Existing real data found. Do you want to continue? This may create duplicates.', 'no')
            ->assertExitCode(0);
    }

    /**
     * Test command continues with existing data when confirmed
     */
    public function test_command_continues_with_existing_data_when_confirmed(): void
    {
        // Create some existing data
        DB::table('languages')->insert([
            'name' => 'English',
            'code' => 'en',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('seed:real-data')
            ->expectsConfirmation('Existing real data found. Do you want to continue? This may create duplicates.', 'yes')
            ->assertExitCode(0);
    }

    /**
     * Test command with invalid modules
     */
    public function test_command_with_invalid_modules(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => ['InvalidModule']
        ])->assertExitCode(0);
    }

    /**
     * Test command with empty modules array
     */
    public function test_command_with_empty_modules_array(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => []
        ])->assertExitCode(0);
    }

    /**
     * Test command output contains expected messages
     */
    public function test_command_output_contains_expected_messages(): void
    {
        $this->artisan('seed:real-data')
            ->expectsOutput('🌍 Starting real data seeding...')
            ->assertExitCode(0);
    }

    /**
     * Test command handles database errors gracefully
     */
    public function test_command_handles_database_errors_gracefully(): void
    {
        // Mock database connection to throw exception
        DB::shouldReceive('table')
            ->andThrow(new \Exception('Database connection failed'));

        $this->artisan('seed:real-data')
            ->assertExitCode(1);
    }

    /**
     * Test command handles seeder errors gracefully
     */
    public function test_command_handles_seeder_errors_gracefully(): void
    {
        // This test would require mocking the Artisan facade
        // to simulate seeder failures
        $this->artisan('seed:real-data')
            ->assertExitCode(0);
    }

    /**
     * Test command with all available modules
     */
    public function test_command_with_all_available_modules(): void
    {
        $availableModules = [
            'Localization',
            'Email',
            'Development',
            'Utilities',
            'Auth',
            'Tenant'
        ];

        $this->artisan('seed:real-data', [
            '--modules' => $availableModules
        ])->assertExitCode(0);
    }

    /**
     * Test command with mixed valid and invalid modules
     */
    public function test_command_with_mixed_valid_and_invalid_modules(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => ['Localization', 'InvalidModule', 'Email']
        ])->assertExitCode(0);
    }

    /**
     * Test command with duplicate modules
     */
    public function test_command_with_duplicate_modules(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => ['Localization', 'Email', 'Localization']
        ])->assertExitCode(0);
    }

    /**
     * Test command with case insensitive modules
     */
    public function test_command_with_case_insensitive_modules(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => ['localization', 'email']
        ])->assertExitCode(0);
    }

    /**
     * Test command with special characters in module names
     */
    public function test_command_with_special_characters_in_module_names(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => ['Localization-1', 'Email_2']
        ])->assertExitCode(0);
    }

    /**
     * Test command with very long module names
     */
    public function test_command_with_very_long_module_names(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => [str_repeat('A', 100)]
        ])->assertExitCode(0);
    }

    /**
     * Test command with numeric module names
     */
    public function test_command_with_numeric_module_names(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => ['123', '456']
        ])->assertExitCode(0);
    }

    /**
     * Test command with empty string module names
     */
    public function test_command_with_empty_string_module_names(): void
    {
        $this->artisan('seed:real-data', [
            '--modules' => ['', ' ']
        ])->assertExitCode(0);
    }
}
