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
        // Add missing properties to cities
        Schema::table('cities', function (Blueprint $table) {
            $table->string('postal_code')->nullable()->after('name');
            $table->boolean('is_capital')->default(false)->after('postal_code');
            $table->string('phone_code')->nullable()->after('is_capital');
            $table->string('timezone')->nullable()->after('phone_code');
        });

        // Add missing properties to provinces
        Schema::table('provinces', function (Blueprint $table) {
            $table->string('currency_code', 3)->nullable()->after('population');
            $table->string('currency_symbol', 10)->nullable()->after('currency_code');
            $table->string('language_code', 5)->nullable()->after('currency_symbol');
        });

        // Add missing properties to streets
        Schema::table('streets', function (Blueprint $table) {
            $table->string('postal_code')->nullable()->after('name');
            $table->decimal('area_km2', 15, 2)->nullable()->after('longitude');
            $table->bigInteger('population')->nullable()->after('area_km2');
            $table->decimal('elevation_m', 8, 2)->nullable()->after('population');
            $table->string('phone_code')->nullable()->after('elevation_m');
            $table->string('timezone')->nullable()->after('phone_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove properties from cities
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'is_capital', 'phone_code', 'timezone']);
        });

        // Remove properties from provinces
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'currency_symbol', 'language_code']);
        });

        // Remove properties from streets
        Schema::table('streets', function (Blueprint $table) {
            $table->dropColumn(['postal_code', 'area_km2', 'population', 'elevation_m', 'phone_code', 'timezone']);
        });
    }
};
