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
        Schema::table('modules', function (Blueprint $table) {
            $table->decimal('base_price', 12, 2)->nullable()->after('slogan');
            $table->foreignId('currency_id')->nullable()->after('base_price')->constrained('currencies')->onDelete('set null');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially', 'lifetime'])->default('monthly')->after('currency_id');
            $table->boolean('is_addon')->default(true)->after('billing_cycle');
            $table->unsignedInteger('trial_days')->default(0)->after('is_addon');
            $table->json('included_modules')->nullable()->after('trial_days')->comment('Module keys included when this is a bundle/plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn([
                'base_price',
                'currency_id',
                'billing_cycle',
                'is_addon',
                'trial_days',
                'included_modules',
            ]);
        });
    }
};
