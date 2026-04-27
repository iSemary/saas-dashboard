<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_metas', function (Blueprint $table) {
            $table->text('meta_value')->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_metas', function (Blueprint $table) {
            $table->string('meta_value')->change();
        });
    }
};
