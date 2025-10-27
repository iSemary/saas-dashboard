<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_upgrade_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('to_plan_id')->constrained('plans')->onDelete('cascade');
            $table->enum('rule_type', ['upgrade', 'downgrade', 'sidegrade'])->default('upgrade');
            $table->boolean('is_allowed')->default(true);
            $table->enum('proration_type', ['immediate', 'next_cycle', 'prorated', 'credit'])->default('prorated');
            $table->decimal('upgrade_fee', 10, 2)->default(0)->comment('One-time fee for upgrade');
            $table->decimal('downgrade_credit', 10, 2)->default(0)->comment('Credit given for downgrade');
            $table->integer('restriction_days')->nullable()->comment('Minimum days before allowing change');
            $table->integer('max_changes_per_period')->nullable()->comment('Max changes allowed per billing period');
            $table->json('required_conditions')->nullable()->comment('Conditions that must be met for change');
            $table->text('change_description')->nullable();
            $table->text('user_message')->nullable()->comment('Message shown to user about this change');
            $table->boolean('requires_approval')->default(false);
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['from_plan_id', 'to_plan_id']);
            $table->index(['from_plan_id', 'rule_type', 'status']);
            $table->index(['to_plan_id', 'rule_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_upgrade_rules');
    }
};
