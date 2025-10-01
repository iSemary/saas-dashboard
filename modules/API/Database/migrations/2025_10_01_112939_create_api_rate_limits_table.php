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
        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // API key, IP address, or user ID
            $table->string('type', 20); // 'api_key', 'ip', 'user'
            $table->string('endpoint')->nullable();
            $table->integer('requests_count')->default(0);
            $table->timestamp('window_start');
            $table->integer('limit')->default(1000);
            $table->string('period', 10)->default('hour');
            $table->timestamp('reset_at');
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('blocked_until')->nullable();
            $table->text('block_reason')->nullable();
            $table->json('custom_fields')->nullable();
            $table->timestamps();

            $table->index(['identifier', 'type', 'endpoint']);
            $table->index(['window_start', 'reset_at']);
            $table->index('is_blocked');
            $table->index('blocked_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_rate_limits');
    }
};
