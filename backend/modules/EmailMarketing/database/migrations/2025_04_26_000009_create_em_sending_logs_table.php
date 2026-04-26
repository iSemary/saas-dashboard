<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('em_sending_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('em_campaigns')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('em_contacts')->nullOnDelete();
            $table->enum('status', ['queued', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'failed', 'unsubscribed'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->text('failed_reason')->nullable();
            $table->string('message_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
            $table->index('contact_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('em_sending_logs');
    }
};
