<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_trials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('country_code', 2)->nullable()->comment('Country-specific trial periods');
            $table->integer('trial_days')->default(0);
            $table->boolean('requires_payment_method')->default(false);
            $table->boolean('auto_convert')->default(true)->comment('Auto convert to paid after trial');
            $table->enum('trial_type', ['free', 'paid', 'freemium'])->default('free');
            $table->decimal('trial_price', 10, 2)->default(0)->comment('Price for paid trials');
            $table->text('trial_features')->nullable()->comment('Features available during trial');
            $table->json('trial_limits')->nullable()->comment('Usage limits during trial');
            $table->text('trial_terms')->nullable()->comment('Trial terms and conditions');
            $table->boolean('allow_multiple_trials')->default(false)->comment('Allow same user multiple trials');
            $table->integer('grace_period_days')->default(0)->comment('Grace period after trial expires');
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['plan_id', 'country_code']);
            $table->index(['plan_id', 'status']);
            $table->index(['trial_days', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_trials');
    }
};
