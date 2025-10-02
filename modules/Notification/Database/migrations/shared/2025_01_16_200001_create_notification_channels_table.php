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
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id()->comment('Primary key of the notification channel');
            $table->unsignedBigInteger('user_id')->comment('Foreign key referencing the users table');
            $table->enum('channel_type', ['web', 'push', 'email'])->comment('Type of notification channel');
            $table->json('subscription_data')->nullable()->comment('Channel-specific subscription data (e.g., push subscription details)');
            $table->boolean('is_active')->default(true)->comment('Whether the channel is active for the user');
            $table->timestamp('subscribed_at')->useCurrent()->comment('When the user subscribed to this channel');
            $table->timestamp('last_used_at')->nullable()->comment('When this channel was last used to send a notification');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'channel_type']);
            $table->index(['user_id', 'is_active']);
            $table->unique(['user_id', 'channel_type']);
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_channels');
    }
};
