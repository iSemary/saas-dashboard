<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sm_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('trigger_type', ['contact_added', 'sms_sent', 'sms_delivered', 'sms_failed', 'opted_out'])->default('contact_added');
            $table->json('conditions')->nullable();
            $table->enum('action_type', ['send_campaign', 'add_to_list', 'remove_from_list', 'webhook'])->default('send_campaign');
            $table->json('action_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('trigger_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sm_automation_rules');
    }
};
