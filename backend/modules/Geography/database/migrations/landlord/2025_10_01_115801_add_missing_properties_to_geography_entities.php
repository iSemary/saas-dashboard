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
            if (!Schema::hasColumn('cities', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('name');
            }
            if (!Schema::hasColumn('cities', 'is_capital')) {
                $table->boolean('is_capital')->default(false)->after('postal_code');
            }
            if (!Schema::hasColumn('cities', 'phone_code')) {
                $table->string('phone_code')->nullable()->after('is_capital');
            }
            if (!Schema::hasColumn('cities', 'timezone')) {
                $table->string('timezone')->nullable()->after('phone_code');
            }
        });

        // Add missing properties to provinces
        Schema::table('provinces', function (Blueprint $table) {
            if (!Schema::hasColumn('provinces', 'currency_code')) {
                $table->string('currency_code', 3)->nullable()->after('population');
            }
            if (!Schema::hasColumn('provinces', 'currency_symbol')) {
                $table->string('currency_symbol', 10)->nullable()->after('currency_code');
            }
            if (!Schema::hasColumn('provinces', 'language_code')) {
                $table->string('language_code', 5)->nullable()->after('currency_symbol');
            }
        });

        // Add missing properties to streets
        Schema::table('streets', function (Blueprint $table) {
            if (!Schema::hasColumn('streets', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('name');
            }
            if (!Schema::hasColumn('streets', 'area_km2')) {
                $table->decimal('area_km2', 15, 2)->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('streets', 'population')) {
                $table->bigInteger('population')->nullable()->after('area_km2');
            }
            if (!Schema::hasColumn('streets', 'elevation_m')) {
                $table->decimal('elevation_m', 8, 2)->nullable()->after('population');
            }
            if (!Schema::hasColumn('streets', 'phone_code')) {
                $table->string('phone_code')->nullable()->after('elevation_m');
            }
            if (!Schema::hasColumn('streets', 'timezone')) {
                $table->string('timezone')->nullable()->after('phone_code');
            }
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
