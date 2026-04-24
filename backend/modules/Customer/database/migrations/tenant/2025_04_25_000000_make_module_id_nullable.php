<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to make module_id nullable (avoids doctrine/dbal requirement)
        DB::statement('ALTER TABLE brand_modules MODIFY module_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE brand_modules MODIFY module_id BIGINT UNSIGNED NOT NULL');
    }
};
