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
        Schema::create('ticket_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->unsignedBigInteger('changed_by');
            $table->text('comment')->nullable();
            $table->integer('time_in_previous_status')->nullable(); // Time in seconds
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['ticket_id', 'created_at']);
            $table->index(['changed_by', 'created_at']);
            $table->index('new_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_status_logs');
    }
};
