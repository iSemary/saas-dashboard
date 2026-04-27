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
        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->after('id')->default(1);
                $table->index(['tenant_id', 'status', 'name']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'tenant_id')) {
                $table->dropIndex(['tenant_id', 'status', 'name']);
                $table->dropColumn('tenant_id');
            }
        });
    }
};
