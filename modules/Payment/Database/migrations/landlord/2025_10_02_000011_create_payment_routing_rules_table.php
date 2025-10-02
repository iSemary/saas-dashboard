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
        Schema::create('payment_routing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Human-readable name for the routing rule');
            $table->text('description')->nullable()->comment('Description of what this rule does');
            $table->json('conditions')->comment('Conditions that trigger this rule (currency, amount, country, etc.)');
            $table->integer('priority')->default(0)->comment('Rule priority (higher number = higher priority)');
            $table->foreignId('target_payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->foreignId('fallback_payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->enum('rule_type', ['primary', 'fallback', 'load_balancing', 'cost_optimization', 'geographic', 'ab_test'])->default('primary');
            $table->decimal('traffic_percentage', 5, 2)->default(100.00)->comment('Percentage of traffic to route through this rule');
            $table->json('time_restrictions')->nullable()->comment('Time-based restrictions for rule activation');
            $table->json('amount_restrictions')->nullable()->comment('Amount-based restrictions');
            $table->json('geographic_restrictions')->nullable()->comment('Geographic restrictions');
            $table->json('customer_segment_restrictions')->nullable()->comment('Customer segment restrictions');
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();
            $table->integer('success_count')->default(0)->comment('Number of successful transactions using this rule');
            $table->integer('failure_count')->default(0)->comment('Number of failed transactions using this rule');
            $table->decimal('success_rate', 5, 2)->default(0)->comment('Success rate percentage for this rule');
            $table->json('metadata')->nullable()->comment('Additional rule metadata');
            $table->timestamps();
            
            $table->index(['is_active', 'priority']);
            $table->index(['rule_type', 'is_active']);
            $table->index(['effective_from', 'effective_until']);
            $table->index(['target_payment_method_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_routing_rules');
    }
};
