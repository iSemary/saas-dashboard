<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add brand_id to survey_surveys table
        Schema::table('survey_surveys', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });

        // Add brand_id to survey_responses table
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });

        // Add brand_id to survey_templates table
        Schema::table('survey_templates', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::table('survey_surveys', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('survey_templates', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }
};
