<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->unsignedBigInteger('module_id'); // References landlord modules table (cross-DB, no FK)
            $table->string('module_key'); // Denormalized from landlord modules for easy querying
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('color_palette')->nullable(); // Module-specific color palette
            $table->json('module_config')->nullable(); // Module-specific settings
            $table->datetime('subscribed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['brand_id', 'module_id']);
            $table->index('module_key');
            $table->index('status');
            $table->unique(['brand_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_modules');
    }
};
