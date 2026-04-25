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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignId('page_id')->constrained('survey_pages')->cascadeOnDelete();
            $table->enum('type', [
                'text', 'textarea', 'email', 'number', 'phone', 'url', 'date',
                'multiple_choice', 'checkbox', 'dropdown',
                'rating', 'nps', 'likert_scale', 'matrix', 'slider',
                'file_upload', 'image_choice', 'ranking', 'yes_no', 'signature'
            ]);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(1);
            $table->json('config')->nullable();
            $table->json('validation')->nullable();
            $table->json('branching')->nullable();
            $table->json('correct_answer')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->index(['survey_id', 'page_id', 'order']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
