<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('hr_employees');
            $table->string('type')->default('praise'); // praise, constructive, peer_review
            $table->string('category')->nullable(); // communication, teamwork, technical, etc.
            $table->text('content');
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['recipient_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_feedback');
    }
};
