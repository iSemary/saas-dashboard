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
        Schema::create('survey_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('colors'); // {primary, secondary, background, text, accent}
            $table->string('font_family')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('background_image_url')->nullable();
            $table->json('button_style')->nullable();
            $table->boolean('is_system')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('is_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_themes');
    }
};
