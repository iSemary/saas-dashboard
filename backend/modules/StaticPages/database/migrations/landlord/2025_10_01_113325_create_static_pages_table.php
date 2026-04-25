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
        Schema::create('static_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('body')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->string('type')->default('page'); // policy, about_us, landing_page, blog, etc.
            $table->string('image')->nullable(); // banner or thumbnail image
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('is_public')->default(true);
            $table->unsignedBigInteger('author_id')->nullable();
            $table->integer('revision')->default(1);
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['slug', 'status']);
            $table->index(['type', 'status']);
            $table->index(['is_public', 'status']);
            $table->index(['author_id', 'status']);
            $table->index(['parent_id', 'order']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_pages');
    }
};
