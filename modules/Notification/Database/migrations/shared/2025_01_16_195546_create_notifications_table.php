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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->comment('Primary key of the notification');
            $table->unsignedBigInteger('user_id')->constrained()->comment('Foreign key referencing the users table');
            $table->integer('module_id')->nullable()->constrained()->comment('Foreign key referencing the modules table (if applicable)');
            $table->string('name')->comment('The name or title of the notification');
            $table->text('description')->comment('The detailed description of the notification');
            $table->enum('type', ['info', 'alert', 'announcement'])->default('info')->comment('The type of notification (e.g., info, alert)');
            $table->string('route')->nullable()->comment('URL route for redirection when the notification is clicked');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low')->comment('Priority of the notification (e.g., normal, high)');
            $table->string('icon')->nullable()->comment('Icon associated with the notification');
            $table->json('metadata')->nullable()->comment('Additional data related to the notification stored as JSON');
            $table->timestamp('seen_at')->nullable()->comment('Timestamp when the notification was seen by the user');
            $table->softDeletes()->comment('Soft delete column for the notification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
