<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_recipient_id')->constrained('email_recipients')->nullable();
            $table->foreignId('email_template_log_id')->constrained('email_template_logs')->nullable();
            $table->bigInteger('email_campaign_id')->constrained('email_campaigns')->nullable();
            $table->bigInteger('email_credential_id')->constrained('email_credentials')->nullable();
            $table->string('email');
            $table->enum('status', ['sent', 'processing', 'failed'])->default('processing');
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->text('error_message')->nullable();
            $table->json('email_recipient_meta')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
