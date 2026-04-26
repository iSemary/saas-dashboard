<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('features_summary');
            $table->string('currency', 10)->default('USD')->after('price');
            $table->string('billing_period', 50)->default('monthly')->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['price', 'currency', 'billing_period']);
        });
    }
};
