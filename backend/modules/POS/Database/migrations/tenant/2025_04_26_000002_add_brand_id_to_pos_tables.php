<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add brand_id to pos_products table
        Schema::table('pos_products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });

        // Add brand_id to pos_categories table
        Schema::table('pos_categories', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });

        // Add brand_id to pos_sub_categories table
        Schema::table('pos_sub_categories', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });

        // Add brand_id to pos_barcodes table
        Schema::table('pos_barcodes', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });

        // Add brand_id to pos_tags table
        Schema::table('pos_tags', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });

        // Add brand_id to pos_damaged table
        Schema::table('pos_damaged', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });

        // Add brand_id to pos_offer_prices table
        Schema::table('pos_offer_prices', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('set null');
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::table('pos_products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('pos_categories', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('pos_sub_categories', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('pos_barcodes', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('pos_tags', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('pos_damaged', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });

        Schema::table('pos_offer_prices', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }
};
