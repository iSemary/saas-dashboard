<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_modules', function (Blueprint $table) {
            // Drop the old unique index on (brand_id, module_id)
            $table->dropUnique(['brand_id', 'module_id']);
            // Add new unique index on (brand_id, module_key) which makes more sense
            $table->unique(['brand_id', 'module_key']);
        });
    }

    public function down(): void
    {
        Schema::table('brand_modules', function (Blueprint $table) {
            $table->dropUnique(['brand_id', 'module_key']);
            $table->unique(['brand_id', 'module_id']);
        });
    }
};
