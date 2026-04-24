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
        // Add coordinates to countries
        Schema::table('countries', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('timezone');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        // Add coordinates to provinces
        Schema::table('provinces', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('timezone');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        // Add coordinates to cities
        Schema::table('cities', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('province_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        // Add coordinates to towns
        Schema::table('towns', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('postalcode');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        // Add coordinates to streets
        Schema::table('streets', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('town_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove coordinates from countries
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        // Remove coordinates from provinces
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        // Remove coordinates from cities
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        // Remove coordinates from towns
        Schema::table('towns', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        // Remove coordinates from streets
        Schema::table('streets', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
