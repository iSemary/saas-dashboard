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
        // Add additional properties to countries
        Schema::table('countries', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable()->after('longitude');
            $table->string('currency_symbol', 10)->nullable()->after('currency_code');
            $table->string('language_code', 5)->nullable()->after('currency_symbol');
            $table->decimal('area_km2', 15, 2)->nullable()->after('language_code');
            $table->bigInteger('population')->nullable()->after('area_km2');
        });

        // Add additional properties to provinces
        Schema::table('provinces', function (Blueprint $table) {
            $table->decimal('area_km2', 15, 2)->nullable()->after('longitude');
            $table->bigInteger('population')->nullable()->after('area_km2');
        });

        // Add additional properties to cities
        Schema::table('cities', function (Blueprint $table) {
            $table->decimal('area_km2', 15, 2)->nullable()->after('longitude');
            $table->bigInteger('population')->nullable()->after('area_km2');
            $table->decimal('elevation_m', 8, 2)->nullable()->after('population');
        });

        // Add additional properties to towns
        Schema::table('towns', function (Blueprint $table) {
            $table->decimal('area_km2', 15, 2)->nullable()->after('longitude');
            $table->bigInteger('population')->nullable()->after('area_km2');
            $table->decimal('elevation_m', 8, 2)->nullable()->after('population');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove additional properties from countries
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'currency_symbol', 'language_code', 'area_km2', 'population']);
        });

        // Remove additional properties from provinces
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn(['area_km2', 'population']);
        });

        // Remove additional properties from cities
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['area_km2', 'population', 'elevation_m']);
        });

        // Remove additional properties from towns
        Schema::table('towns', function (Blueprint $table) {
            $table->dropColumn(['area_km2', 'population', 'elevation_m']);
        });
    }
};
