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
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('survey_responses')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('survey_questions')->restrictOnDelete();
            $table->text('value')->nullable();
            $table->json('selected_options')->nullable();
            $table->foreignId('file_id')->nullable(); // References FileManager files table
            $table->json('matrix_answers')->nullable();
            $table->integer('rating_value')->nullable();
            $table->integer('computed_score')->nullable();
            $table->timestamps();

            $table->index(['response_id', 'question_id']);
            $table->index('file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
    }
};
