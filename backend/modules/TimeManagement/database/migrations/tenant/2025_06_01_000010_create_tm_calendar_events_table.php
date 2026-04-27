<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tm_calendar_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_all_day')->default(false);
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('meeting_provider')->nullable(); // google_meet, teams, zoom
            $table->string('recurrence_rule')->nullable(); // RRULE format
            $table->string('source')->default('manual'); // manual, synced
            $table->string('external_event_id')->nullable(); // ID from Google/Outlook
            $table->string('provider')->nullable(); // google, outlook
            $table->json('attendees')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'user_id', 'starts_at']);
            $table->index(['provider', 'external_event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tm_calendar_events');
    }
};
