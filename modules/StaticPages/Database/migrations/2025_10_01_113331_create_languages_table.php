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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // English, Arabic, French, etc.
            $table->string('code', 5)->unique(); // en, ar, fr, etc. (ISO 639-1)
            $table->string('native_name'); // English, العربية, Français, etc.
            $table->string('flag')->nullable(); // flag emoji or image path
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->string('direction', 3)->default('ltr'); // ltr, rtl
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i:s');
            $table->string('currency_code', 3)->nullable(); // USD, EUR, etc.
            $table->json('locale_settings')->nullable(); // additional locale settings
            $table->integer('sort_order')->default(0);
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index(['is_default', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
