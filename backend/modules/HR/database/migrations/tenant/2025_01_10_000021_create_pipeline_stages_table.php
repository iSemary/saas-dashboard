<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->string('maps_to_status')->nullable(); // new, screening, interview, offer, hired
            $table->boolean('is_default')->default(false);
            $table->boolean('requires_interview')->default(false);
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('auto_email_template_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('order');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_pipeline_stages');
    }
};
