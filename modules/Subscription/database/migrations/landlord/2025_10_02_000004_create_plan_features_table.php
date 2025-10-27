<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('feature_key')->comment('Unique identifier for the feature');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('feature_type', ['boolean', 'numeric', 'text', 'json'])->default('boolean');
            $table->text('feature_value')->nullable()->comment('JSON or text value depending on type');
            $table->integer('numeric_limit')->nullable()->comment('For numeric features like storage, users, etc.');
            $table->boolean('is_unlimited')->default(false);
            $table->string('unit')->nullable()->comment('Unit for numeric features (GB, users, etc.)');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_highlighted')->default(false);
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['plan_id', 'feature_key']);
            $table->index(['plan_id', 'status', 'sort_order']);
            $table->index('feature_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};
