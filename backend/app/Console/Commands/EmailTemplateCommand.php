<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Email\Entities\EmailTemplate;
use Modules\Email\Services\EmailTemplateService;

class EmailTemplateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:templates 
                            {action : The action to perform (list|create|update|delete|seed)}
                            {--name= : Template name}
                            {--subject= : Email subject}
                            {--description= : Template description}
                            {--body= : Email body content}
                            {--status=active : Template status (active|inactive)}
                            {--id= : Template ID for update/delete operations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage email templates dynamically';

    protected $emailTemplateService;

    public function __construct(EmailTemplateService $emailTemplateService)
    {
        parent::__construct();
        $this->emailTemplateService = $emailTemplateService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->listTemplates();
                break;
            case 'create':
                $this->createTemplate();
                break;
            case 'update':
                $this->updateTemplate();
                break;
            case 'delete':
                $this->deleteTemplate();
                break;
            case 'seed':
                $this->seedTemplates();
                break;
            default:
                $this->error('Invalid action. Available actions: list, create, update, delete, seed');
                return 1;
        }

        return 0;
    }

    /**
     * List all email templates
     */
    private function listTemplates()
    {
        $templates = $this->emailTemplateService->getAll();
        
        if ($templates->isEmpty()) {
            $this->info('No email templates found.');
            return;
        }

        $headers = ['ID', 'Name', 'Subject', 'Status', 'Created At'];
        $rows = [];

        foreach ($templates as $template) {
            $rows[] = [
                $template->id,
                $template->name,
                $template->subject,
                $template->status,
                $template->created_at->format('Y-m-d H:i:s')
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Create a new email template
     */
    private function createTemplate()
    {
        $name = $this->option('name') ?: $this->ask('Template name');
        $subject = $this->option('subject') ?: $this->ask('Email subject');
        $description = $this->option('description') ?: $this->ask('Template description');
        $body = $this->option('body') ?: $this->ask('Email body content');
        $status = $this->option('status');

        if (!$name || !$subject || !$description || !$body) {
            $this->error('All fields are required: name, subject, description, body');
            return;
        }

        try {
            $template = $this->emailTemplateService->create([
                'name' => $name,
                'subject' => $subject,
                'description' => $description,
                'body' => $body,
                'status' => $status
            ]);

            $this->info("Email template '{$name}' created successfully with ID: {$template->id}");
        } catch (\Exception $e) {
            $this->error('Failed to create template: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing email template
     */
    private function updateTemplate()
    {
        $id = $this->option('id') ?: $this->ask('Template ID to update');
        
        if (!$id) {
            $this->error('Template ID is required for update operation');
            return;
        }

        $template = $this->emailTemplateService->get($id);
        if (!$template) {
            $this->error("Template with ID {$id} not found");
            return;
        }

        $data = [];
        
        if ($name = $this->option('name')) {
            $data['name'] = $name;
        }
        if ($subject = $this->option('subject')) {
            $data['subject'] = $subject;
        }
        if ($description = $this->option('description')) {
            $data['description'] = $description;
        }
        if ($body = $this->option('body')) {
            $data['body'] = $body;
        }
        if ($status = $this->option('status')) {
            $data['status'] = $status;
        }

        if (empty($data)) {
            $this->error('No fields provided for update');
            return;
        }

        try {
            $this->emailTemplateService->update($id, $data);
            $this->info("Template '{$template->name}' updated successfully");
        } catch (\Exception $e) {
            $this->error('Failed to update template: ' . $e->getMessage());
        }
    }

    /**
     * Delete an email template
     */
    private function deleteTemplate()
    {
        $id = $this->option('id') ?: $this->ask('Template ID to delete');
        
        if (!$id) {
            $this->error('Template ID is required for delete operation');
            return;
        }

        $template = $this->emailTemplateService->get($id);
        if (!$template) {
            $this->error("Template with ID {$id} not found");
            return;
        }

        if ($this->confirm("Are you sure you want to delete template '{$template->name}'?")) {
            try {
                $this->emailTemplateService->delete($id);
                $this->info("Template '{$template->name}' deleted successfully");
            } catch (\Exception $e) {
                $this->error('Failed to delete template: ' . $e->getMessage());
            }
        }
    }

    /**
     * Seed email templates
     */
    private function seedTemplates()
    {
        if ($this->confirm('This will seed default email templates. Continue?')) {
            try {
                $seeder = new \Modules\Email\Database\Seeders\EmailTemplateSeeder();
                $seeder->run();
                $this->info('Email templates seeded successfully!');
            } catch (\Exception $e) {
                $this->error('Failed to seed templates: ' . $e->getMessage());
            }
        }
    }
}
