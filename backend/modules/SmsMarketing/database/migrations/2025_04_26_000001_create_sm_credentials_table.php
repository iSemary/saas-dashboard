<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sm_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('provider', ['twilio', 'vonage', 'messagebird', 'mock'])->default('mock');
            $table->string('account_sid')->nullable();
            $table->text('auth_token')->nullable();
            $table->string('from_number');
            $table->string('webhook_url')->nullable();
            $table->boolean('is_default')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sm_credentials');
    }
};
