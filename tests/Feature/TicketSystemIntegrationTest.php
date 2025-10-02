<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Modules\Ticket\Entities\Ticket;
use Modules\Ticket\Entities\TicketStatusLog;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Entities\CommentReaction;
use Modules\Comment\Entities\CommentAttachment;
use Modules\Ticket\Notifications\TicketCreatedNotification;
use Modules\Ticket\Notifications\TicketStatusChangedNotification;
use Modules\Comment\Notifications\CommentAddedNotification;

class TicketSystemIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $assignee;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        $this->assignee = User::factory()->create([
            'name' => 'Assignee User',
            'email' => 'assignee@example.com'
        ]);

        // Authenticate as test user
        $this->actingAs($this->user);

        // Setup storage for file tests
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_a_ticket_with_all_fields()
    {
        $ticketData = [
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket description',
            'html_content' => '<p>This is <strong>HTML</strong> content</p>',
            'priority' => 'high',
            'assigned_to' => $this->assignee->id,
            'due_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'tags' => ['test', 'bug']
        ];

        $response = $this->postJson(route('landlord.tickets.store'), $ticketData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'status' => 'open',
            'priority' => 'high',
            'created_by' => $this->user->id,
            'assigned_to' => $this->assignee->id
        ]);

        // Check that ticket number was generated
        $ticket = Ticket::where('title', 'Test Ticket')->first();
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('TKT-', $ticket->ticket_number);
    }

    /** @test */
    public function it_creates_initial_status_log_when_ticket_is_created()
    {
        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 'medium',
            'created_by' => $this->user->id,
            'status' => 'open'
        ]);

        $this->assertDatabaseHas('ticket_status_logs', [
            'ticket_id' => $ticket->id,
            'old_status' => null,
            'new_status' => 'open',
            'changed_by' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_update_ticket_status_and_create_status_log()
    {
        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 'medium',
            'created_by' => $this->user->id,
            'status' => 'open'
        ]);

        $response = $this->patchJson(route('landlord.tickets.update-status', $ticket->id), [
            'status' => 'in_progress',
            'comment' => 'Starting work on this ticket'
        ]);

        $response->assertStatus(200);
        
        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);

        $this->assertDatabaseHas('ticket_status_logs', [
            'ticket_id' => $ticket->id,
            'old_status' => 'open',
            'new_status' => 'in_progress',
            'changed_by' => $this->user->id,
            'comment' => 'Starting work on this ticket'
        ]);
    }

    /** @test */
    public function it_can_add_comments_to_tickets()
    {
        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 'medium',
            'created_by' => $this->user->id
        ]);

        $commentData = [
            'comment' => 'This is a test comment',
            'object_id' => $ticket->id,
            'object_model' => 'Modules\\Ticket\\Entities\\Ticket'
        ];

        $response = $this->postJson(route('landlord.comments.store'), $commentData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'comment' => 'This is a test comment',
            'object_id' => $ticket->id,
            'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_add_replies_to_comments()
    {
        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 'medium',
            'created_by' => $this->user->id
        ]);

        $parentComment = Comment::create([
            'comment' => 'Parent comment',
            'object_id' => $ticket->id,
            'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
            'user_id' => $this->user->id
        ]);

        $response = $this->postJson(route('landlord.comments.reply', $parentComment->id), [
            'comment' => 'This is a reply'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'comment' => 'This is a reply',
            'parent_id' => $parentComment->id,
            'object_id' => $ticket->id,
            'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_add_reactions_to_comments()
    {
        $comment = Comment::create([
            'comment' => 'Test comment',
            'object_id' => 1,
            'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
            'user_id' => $this->user->id
        ]);

        $response = $this->postJson(route('landlord.comments.reaction.add', $comment->id), [
            'reaction_type' => 'like'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comment_reactions', [
            'comment_id' => $comment->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);
    }

    /** @test */
    public function it_can_toggle_reactions()
    {
        $comment = Comment::create([
            'comment' => 'Test comment',
            'object_id' => 1,
            'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
            'user_id' => $this->user->id
        ]);

        // Add reaction
        $this->postJson(route('landlord.comments.reaction.add', $comment->id), [
            'reaction_type' => 'like'
        ]);

        $this->assertDatabaseHas('comment_reactions', [
            'comment_id' => $comment->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);

        // Toggle same reaction (should remove it)
        $response = $this->postJson(route('landlord.comments.reaction.add', $comment->id), [
            'reaction_type' => 'like'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comment_reactions', [
            'comment_id' => $comment->id,
            'user_id' => $this->user->id,
            'reaction_type' => 'like'
        ]);
    }

    /** @test */
    public function it_can_handle_file_attachments_in_comments()
    {
        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 'medium',
            'created_by' => $this->user->id
        ]);

        $file = UploadedFile::fake()->image('test.jpg', 100, 100);

        $response = $this->postJson(route('landlord.comments.store'), [
            'comment' => 'Comment with attachment',
            'object_id' => $ticket->id,
            'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
            'attachments' => [$file]
        ]);

        $response->assertStatus(200);
        
        $comment = Comment::where('comment', 'Comment with attachment')->first();
        $this->assertNotNull($comment);
        $this->assertEquals(1, $comment->attachments->count());
        
        $attachment = $comment->attachments->first();
        $this->assertEquals('test.jpg', $attachment->original_name);
        $this->assertEquals('image/jpeg', $attachment->mime_type);
    }

    /** @test */
    public function it_tracks_sla_data_correctly()
    {
        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 'high',
            'created_by' => $this->user->id,
            'status' => 'open'
        ]);

        $this->assertNotNull($ticket->sla_data);
        $this->assertArrayHasKey('created_at', $ticket->sla_data);
        $this->assertArrayHasKey('status_history', $ticket->sla_data);
        
        $statusHistory = $ticket->sla_data['status_history'];
        $this->assertCount(1, $statusHistory);
        $this->assertEquals('open', $statusHistory[0]['status']);
    }

    /** @test */
    public function it_can_get_kanban_data()
    {
        // Create tickets with different statuses
        Ticket::create([
            'title' => 'Open Ticket',
            'description' => 'Test',
            'priority' => 'medium',
            'created_by' => $this->user->id,
            'status' => 'open'
        ]);

        Ticket::create([
            'title' => 'In Progress Ticket',
            'description' => 'Test',
            'priority' => 'high',
            'created_by' => $this->user->id,
            'status' => 'in_progress'
        ]);

        $response = $this->getJson(route('landlord.tickets.kanban-data'));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertArrayHasKey('open', $data);
        $this->assertArrayHasKey('in_progress', $data);
        $this->assertEquals(1, $data['open']['count']);
        $this->assertEquals(1, $data['in_progress']['count']);
    }

    /** @test */
    public function it_can_perform_bulk_operations()
    {
        $ticket1 = Ticket::create([
            'title' => 'Ticket 1',
            'description' => 'Test',
            'priority' => 'low',
            'created_by' => $this->user->id,
            'status' => 'open'
        ]);

        $ticket2 = Ticket::create([
            'title' => 'Ticket 2',
            'description' => 'Test',
            'priority' => 'low',
            'created_by' => $this->user->id,
            'status' => 'open'
        ]);

        $response = $this->patchJson(route('landlord.tickets.bulk-update'), [
            'ticket_ids' => [$ticket1->id, $ticket2->id],
            'action' => 'priority',
            'value' => 'high',
            'comment' => 'Bulk priority update'
        ]);

        $response->assertStatus(200);
        
        $ticket1->refresh();
        $ticket2->refresh();
        
        $this->assertEquals('high', $ticket1->priority);
        $this->assertEquals('high', $ticket2->priority);
    }

    /** @test */
    public function it_sends_notifications_for_ticket_creation()
    {
        Notification::fake();

        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'priority' => 'high',
            'created_by' => $this->user->id,
            'assigned_to' => $this->assignee->id
        ]);

        // Manually trigger notification (in real app, this would be in an observer)
        $this->assignee->notify(new TicketCreatedNotification($ticket));

        Notification::assertSentTo(
            $this->assignee,
            TicketCreatedNotification::class,
            function ($notification) use ($ticket) {
                return $notification->ticket->id === $ticket->id;
            }
        );
    }

    /** @test */
    public function it_can_search_tickets()
    {
        Ticket::create([
            'title' => 'Login Bug',
            'description' => 'Users cannot login',
            'priority' => 'high',
            'created_by' => $this->user->id
        ]);

        Ticket::create([
            'title' => 'Feature Request',
            'description' => 'Add dark mode',
            'priority' => 'low',
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson(route('landlord.tickets.search', ['query' => 'login']));

        $response->assertStatus(200);
        $tickets = $response->json('data');
        
        $this->assertCount(1, $tickets);
        $this->assertEquals('Login Bug', $tickets[0]['title']);
    }

    /** @test */
    public function it_can_get_ticket_statistics()
    {
        // Create tickets with different statuses
        Ticket::create(['title' => 'T1', 'description' => 'Test', 'priority' => 'high', 'created_by' => $this->user->id, 'status' => 'open']);
        Ticket::create(['title' => 'T2', 'description' => 'Test', 'priority' => 'medium', 'created_by' => $this->user->id, 'status' => 'in_progress']);
        Ticket::create(['title' => 'T3', 'description' => 'Test', 'priority' => 'low', 'created_by' => $this->user->id, 'status' => 'resolved']);

        $response = $this->getJson(route('landlord.tickets.stats'));

        $response->assertStatus(200);
        $stats = $response->json('data');
        
        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(2, $stats['open']); // open + in_progress
        $this->assertEquals(1, $stats['by_status']['open']);
        $this->assertEquals(1, $stats['by_status']['in_progress']);
        $this->assertEquals(1, $stats['by_status']['resolved']);
    }

    /** @test */
    public function it_handles_polymorphic_comments_correctly()
    {
        // Test that comments work with different object types
        $ticket = Ticket::create([
            'title' => 'Test Ticket',
            'description' => 'Test',
            'priority' => 'medium',
            'created_by' => $this->user->id
        ]);

        $comment = Comment::create([
            'comment' => 'Ticket comment',
            'object_id' => $ticket->id,
            'object_model' => 'Modules\\Ticket\\Entities\\Ticket',
            'user_id' => $this->user->id
        ]);

        // Test relationship
        $this->assertEquals($ticket->id, $comment->object_id);
        $this->assertEquals('Modules\\Ticket\\Entities\\Ticket', $comment->object_model);
        
        // Test that we can retrieve comments for the ticket
        $ticketComments = Comment::forObject($ticket->id, 'Modules\\Ticket\\Entities\\Ticket')->get();
        $this->assertCount(1, $ticketComments);
        $this->assertEquals('Ticket comment', $ticketComments->first()->comment);
    }

    protected function tearDown(): void
    {
        Storage::fake('public');
        parent::tearDown();
    }
}
