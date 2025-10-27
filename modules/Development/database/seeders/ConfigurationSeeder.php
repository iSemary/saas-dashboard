<?php

namespace Modules\Development\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Development\Entities\Configuration;
use Modules\Utilities\Entities\Type;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a type for configurations (if available)
        $type = Type::where('slug', 'configuration')->first();

        $configurations = [
            [
                'configuration_key' => 'app.name',
                'configuration_value' => 'SaaS Dashboard',
                'description' => 'Application name displayed in the UI',
                'type_id' => $type?->id,
                'input_type' => 'string',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'app.debug',
                'configuration_value' => 'false',
                'description' => 'Enable debug mode for development',
                'type_id' => $type?->id,
                'input_type' => 'boolean',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'app.timezone',
                'configuration_value' => 'UTC',
                'description' => 'Default timezone for the application',
                'type_id' => $type?->id,
                'input_type' => 'string',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'app.locale',
                'configuration_value' => 'en',
                'description' => 'Default locale for the application',
                'type_id' => $type?->id,
                'input_type' => 'string',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'mail.driver',
                'configuration_value' => 'smtp',
                'description' => 'Mail driver configuration',
                'type_id' => $type?->id,
                'input_type' => 'string',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'mail.host',
                'configuration_value' => 'smtp.gmail.com',
                'description' => 'SMTP host for email sending',
                'type_id' => $type?->id,
                'input_type' => 'string',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'mail.port',
                'configuration_value' => '587',
                'description' => 'SMTP port for email sending',
                'type_id' => $type?->id,
                'input_type' => 'integer',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'mail.username',
                'configuration_value' => 'noreply@example.com',
                'description' => 'SMTP username for email authentication',
                'type_id' => $type?->id,
                'input_type' => 'email',
                'is_encrypted' => true,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'mail.password',
                'configuration_value' => 'your-email-password',
                'description' => 'SMTP password for email authentication',
                'type_id' => $type?->id,
                'input_type' => 'password',
                'is_encrypted' => true,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'database.backup.enabled',
                'configuration_value' => 'true',
                'description' => 'Enable automatic database backups',
                'type_id' => $type?->id,
                'input_type' => 'boolean',
                'is_encrypted' => false,
                'is_system' => false,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'database.backup.frequency',
                'configuration_value' => 'daily',
                'description' => 'Frequency of automatic database backups',
                'type_id' => $type?->id,
                'input_type' => 'string',
                'is_encrypted' => false,
                'is_system' => false,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'security.max_login_attempts',
                'configuration_value' => '5',
                'description' => 'Maximum login attempts before account lockout',
                'type_id' => $type?->id,
                'input_type' => 'integer',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'security.session_timeout',
                'configuration_value' => '3600',
                'description' => 'Session timeout in seconds',
                'type_id' => $type?->id,
                'input_type' => 'integer',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'security.2fa_required',
                'configuration_value' => 'false',
                'description' => 'Require two-factor authentication for all users',
                'type_id' => $type?->id,
                'input_type' => 'boolean',
                'is_encrypted' => false,
                'is_system' => false,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'ui.theme',
                'configuration_value' => 'light',
                'description' => 'Default UI theme (light, dark, auto)',
                'type_id' => $type?->id,
                'input_type' => 'string',
                'is_encrypted' => false,
                'is_system' => false,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'ui.primary_color',
                'configuration_value' => '#3B82F6',
                'description' => 'Primary color for the UI theme',
                'type_id' => $type?->id,
                'input_type' => 'color',
                'is_encrypted' => false,
                'is_system' => false,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'ui.items_per_page',
                'configuration_value' => '25',
                'description' => 'Default number of items per page in lists',
                'type_id' => $type?->id,
                'input_type' => 'integer',
                'is_encrypted' => false,
                'is_system' => false,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'api.rate_limit',
                'configuration_value' => '1000',
                'description' => 'API rate limit per hour per user',
                'type_id' => $type?->id,
                'input_type' => 'integer',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'api.version',
                'configuration_value' => 'v1',
                'description' => 'Current API version',
                'type_id' => $type?->id,
                'input_type' => 'string',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
            [
                'configuration_key' => 'storage.max_file_size',
                'configuration_value' => '10485760',
                'description' => 'Maximum file upload size in bytes (10MB)',
                'type_id' => $type?->id,
                'input_type' => 'integer',
                'is_encrypted' => false,
                'is_system' => true,
                'is_visible' => true,
            ],
        ];

        foreach ($configurations as $configData) {
            Configuration::create($configData);
        }

        $this->command->info('Configurations seeded successfully!');
    }
}
