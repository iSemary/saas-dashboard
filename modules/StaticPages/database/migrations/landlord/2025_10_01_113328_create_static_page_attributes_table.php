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
        Schema::create('static_page_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('static_page_id');
            $table->string('key'); // e.g., 'content', 'title', 'subtitle'
            $table->longText('value'); // translatable content
            $table->string('language_code', 5)->default('en'); // ISO 639-1 language code
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('metadata')->nullable(); // additional metadata for the attribute
            $table->softDeletes();
            $table->timestamps();

            $table->index(['static_page_id', 'key', 'language_code']);
            $table->index(['static_page_id', 'status']);
            $table->index(['language_code', 'status']);
            $table->index('key');
            
            $table->foreign('static_page_id')->references('id')->on('static_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_page_attributes');
    }
};
