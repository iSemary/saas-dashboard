<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add brand_id column
            $table->foreignId('brand_id')->nullable()->after('id')->constrained('brands')->onDelete('cascade');
            
            // Add index for brand_id
            $table->index('brand_id');
            
            // Note: We keep tenant_id for now for backward compatibility
            // but brand_id will be the primary relationship
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }
};
