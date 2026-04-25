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
        if (!Schema::hasTable('payment_method_configurations')) {
            Schema::create('payment_method_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->enum('environment', ['sandbox', 'production'])->default('sandbox');
            $table->string('config_key')->comment('Configuration parameter name');
            $table->text('config_value')->nullable()->comment('Configuration value (encrypted if sensitive)');
            $table->boolean('is_secret')->default(false)->comment('Whether this config contains sensitive data');
            $table->enum('config_type', ['string', 'number', 'boolean', 'json', 'url', 'email'])->default('string');
            $table->text('description')->nullable()->comment('Description of this configuration parameter');
            $table->boolean('is_required')->default(false)->comment('Whether this config is required for gateway operation');
            $table->string('validation_rules')->nullable()->comment('Validation rules for this config');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['payment_method_id', 'environment', 'config_key'], 'pm_config_unique');
            $table->index(['environment', 'status'], 'pm_config_env_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_method_configurations');
    }
};
