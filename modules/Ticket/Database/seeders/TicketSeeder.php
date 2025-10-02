<?php

namespace Modules\Ticket\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ticket\Entities\Ticket;
use Modules\Ticket\Entities\TicketStatusLog;
use App\Models\User;
use Modules\Customer\Entities\Brand;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users and brands for relationships
        $users = User::all();
        $brands = Brand::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping ticket seeding.');
            return;
        }

        // Sample ticket data
        $tickets = [
            [
                'title' => 'Login Issues with Mobile App',
                'description' => 'Users are reporting difficulties logging into the mobile application. The issue seems to occur intermittently and affects both iOS and Android platforms.',
                'html_content' => '<p>Users are reporting difficulties logging into the mobile application.</p><p>The issue seems to occur intermittently and affects both <strong>iOS</strong> and <strong>Android</strong> platforms.</p><ul><li>Error occurs on login screen</li><li>Affects multiple users</li><li>Started yesterday</li></ul>',
                'status' => 'open',
                'priority' => 'high',
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                'tags' => json_encode(['mobile', 'login', 'bug']),
                'due_date' => now()->addDays(3),
                'metadata' => json_encode(['source' => 'customer_support', 'severity' => 'high']),
            ],
            [
                'title' => 'Feature Request: Dark Mode',
                'description' => 'Multiple users have requested a dark mode option for the dashboard. This would improve user experience, especially for users working in low-light environments.',
                'html_content' => '<p>Multiple users have requested a <strong>dark mode</strong> option for the dashboard.</p><p>This would improve user experience, especially for users working in low-light environments.</p><h4>Benefits:</h4><ul><li>Reduced eye strain</li><li>Better battery life on mobile devices</li><li>Modern UI trend</li></ul>',
                'status' => 'in_progress',
                'priority' => 'medium',
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                'tags' => json_encode(['feature', 'ui', 'enhancement']),
                'due_date' => now()->addWeeks(2),
                'metadata' => json_encode(['source' => 'feature_request', 'votes' => 15]),
            ],
            [
                'title' => 'Database Performance Optimization',
                'description' => 'The system is experiencing slow query performance during peak hours. We need to optimize database queries and potentially add indexes.',
                'html_content' => '<p>The system is experiencing <strong>slow query performance</strong> during peak hours.</p><p>We need to optimize database queries and potentially add indexes.</p><h4>Affected Areas:</h4><ul><li>User dashboard loading</li><li>Report generation</li><li>Search functionality</li></ul><h4>Proposed Solutions:</h4><ol><li>Add database indexes</li><li>Optimize slow queries</li><li>Implement query caching</li></ol>',
                'status' => 'on_hold',
                'priority' => 'urgent',
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                'tags' => json_encode(['performance', 'database', 'optimization']),
                'due_date' => now()->addDays(1),
                'metadata' => json_encode(['source' => 'monitoring', 'impact' => 'high']),
            ],
            [
                'title' => 'Email Notification Not Working',
                'description' => 'Users are not receiving email notifications for important events. The email service appears to be functioning, but notifications are not being sent.',
                'html_content' => '<p>Users are not receiving <strong>email notifications</strong> for important events.</p><p>The email service appears to be functioning, but notifications are not being sent.</p><h4>Investigation Results:</h4><ul><li>SMTP server is working</li><li>Email templates are correct</li><li>Queue system needs investigation</li></ul>',
                'status' => 'resolved',
                'priority' => 'high',
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                'tags' => json_encode(['email', 'notifications', 'bug']),
                'due_date' => now()->subDays(1),
                'resolved_at' => now()->subHours(2),
                'metadata' => json_encode(['source' => 'user_report', 'resolution' => 'queue_fix']),
            ],
            [
                'title' => 'API Rate Limiting Implementation',
                'description' => 'Implement rate limiting for API endpoints to prevent abuse and ensure fair usage across all clients.',
                'html_content' => '<p>Implement <strong>rate limiting</strong> for API endpoints to prevent abuse and ensure fair usage across all clients.</p><h4>Requirements:</h4><ul><li>Different limits for different user tiers</li><li>Graceful error handling</li><li>Rate limit headers in responses</li></ul><h4>Endpoints to protect:</h4><ol><li>Authentication endpoints</li><li>Data retrieval endpoints</li><li>File upload endpoints</li></ol>',
                'status' => 'closed',
                'priority' => 'medium',
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                'tags' => json_encode(['api', 'security', 'rate-limiting']),
                'due_date' => now()->subWeeks(1),
                'resolved_at' => now()->subDays(3),
                'closed_at' => now()->subDays(2),
                'metadata' => json_encode(['source' => 'security_review', 'implementation' => 'complete']),
            ],
            [
                'title' => 'User Profile Image Upload Issues',
                'description' => 'Some users are experiencing issues when trying to upload profile images. The upload process fails without clear error messages.',
                'html_content' => '<p>Some users are experiencing issues when trying to upload <strong>profile images</strong>.</p><p>The upload process fails without clear error messages.</p><h4>Error Details:</h4><ul><li>File size validation issues</li><li>Image format restrictions</li><li>Server timeout on large files</li></ul>',
                'status' => 'open',
                'priority' => 'low',
                'created_by' => $users->random()->id,
                'assigned_to' => null, // Unassigned
                'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                'tags' => json_encode(['upload', 'profile', 'images']),
                'due_date' => now()->addWeeks(1),
                'metadata' => json_encode(['source' => 'user_feedback', 'frequency' => 'occasional']),
            ],
            [
                'title' => 'Integration with Third-Party Analytics',
                'description' => 'Integrate the platform with Google Analytics and other third-party analytics tools to provide better insights to users.',
                'html_content' => '<p>Integrate the platform with <strong>Google Analytics</strong> and other third-party analytics tools.</p><p>This will provide better insights to users about their data and usage patterns.</p><h4>Integration Requirements:</h4><ul><li>Google Analytics 4</li><li>Facebook Pixel</li><li>Custom analytics dashboard</li></ul>',
                'status' => 'in_progress',
                'priority' => 'low',
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                'tags' => json_encode(['integration', 'analytics', 'third-party']),
                'due_date' => now()->addMonth(),
                'metadata' => json_encode(['source' => 'product_roadmap', 'complexity' => 'medium']),
            ],
            [
                'title' => 'Security Vulnerability in File Upload',
                'description' => 'A potential security vulnerability has been identified in the file upload functionality. Immediate attention required.',
                'html_content' => '<p>A potential <strong>security vulnerability</strong> has been identified in the file upload functionality.</p><p><strong>Immediate attention required.</strong></p><h4>Vulnerability Details:</h4><ul><li>Insufficient file type validation</li><li>Potential for malicious file execution</li><li>Missing virus scanning</li></ul><h4>Recommended Actions:</h4><ol><li>Implement strict file type validation</li><li>Add virus scanning</li><li>Sandbox uploaded files</li></ol>',
                'status' => 'open',
                'priority' => 'urgent',
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
                'brand_id' => $brands->isNotEmpty() ? $brands->random()->id : null,
                'tags' => json_encode(['security', 'vulnerability', 'urgent']),
                'due_date' => now()->addHours(24),
                'metadata' => json_encode(['source' => 'security_audit', 'severity' => 'critical']),
            ],
        ];

        foreach ($tickets as $ticketData) {
            $ticket = Ticket::create($ticketData);
            
            // Create status logs for tickets that have been updated
            $this->createStatusLogs($ticket, $users);
        }
        
        $this->command->info('✅ Ticket seeding completed successfully.');
    }

    /**
     * Create status logs for tickets
     */
    private function createStatusLogs(Ticket $ticket, $users)
    {
        // Initial status log is created automatically by the model
        
        // Add additional status changes for some tickets
        if (in_array($ticket->status, ['resolved', 'closed', 'in_progress', 'on_hold'])) {
            $statusProgression = $this->getStatusProgression($ticket->status);
            
            foreach ($statusProgression as $index => $status) {
                if ($index === 0) continue; // Skip initial status (already created)
                
                $previousStatus = $index > 0 ? $statusProgression[$index - 1] : null;
                
                TicketStatusLog::create([
                    'ticket_id' => $ticket->id,
                    'old_status' => $previousStatus,
                    'new_status' => $status,
                    'changed_by' => $users->random()->id,
                    'comment' => $this->getStatusChangeComment($status),
                    'created_at' => $ticket->created_at->addHours($index * 2), // Spread changes over time
                ]);
            }
        }
    }

    /**
     * Get status progression for a final status
     */
    private function getStatusProgression($finalStatus)
    {
        $progressions = [
            'in_progress' => ['open', 'in_progress'],
            'on_hold' => ['open', 'in_progress', 'on_hold'],
            'resolved' => ['open', 'in_progress', 'resolved'],
            'closed' => ['open', 'in_progress', 'resolved', 'closed'],
        ];

        return $progressions[$finalStatus] ?? ['open'];
    }

    /**
     * Get appropriate comment for status change
     */
    private function getStatusChangeComment($status)
    {
        $comments = [
            'in_progress' => 'Started working on this ticket',
            'on_hold' => 'Putting this on hold pending further information',
            'resolved' => 'Issue has been resolved and tested',
            'closed' => 'Ticket closed after confirmation from user',
        ];

        return $comments[$status] ?? 'Status updated';
    }
}
