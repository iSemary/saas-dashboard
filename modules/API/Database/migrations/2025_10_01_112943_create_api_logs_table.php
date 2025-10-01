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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('api_key_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method', 10);
            $table->string('endpoint');
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->integer('status_code');
            $table->integer('response_time_ms')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();
            $table->string('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->index(['api_key_id', 'logged_at']);
            $table->index(['user_id', 'logged_at']);
            $table->index(['method', 'endpoint']);
            $table->index(['status_code', 'logged_at']);
            $table->index('ip_address');
            $table->index('logged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
