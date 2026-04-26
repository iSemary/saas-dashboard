<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('survey_surveys')->cascadeOnDelete();
            $table->enum('channel', ['email', 'link', 'embed', 'sms', 'qr_code', 'social']);
            $table->string('token', 64)->unique();
            $table->json('config')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('uses_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['survey_id', 'channel']);
            $table->index('token');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_shares');
    }
};
