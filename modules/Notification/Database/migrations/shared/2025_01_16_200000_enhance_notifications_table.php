<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add new fields for enhanced notifications
            $table->string('title')->nullable()->after('name')->comment('Title of the notification (replaces name)');
            $table->text('body')->nullable()->after('description')->comment('Body content of the notification (replaces description)');
            $table->boolean('is_read')->default(false)->after('seen_at')->comment('Whether the notification has been read');
            $table->json('data')->nullable()->after('metadata')->comment('Structured notification data (replaces metadata)');
            
            // Add indexes for better performance
            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['title', 'body', 'is_read', 'data']);
            $table->dropIndex(['user_id', 'is_read']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['type', 'created_at']);
        });
    }
};
