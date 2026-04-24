<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('brand_module_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->string('module_key'); // e.g., 'crm', 'hr', 'accounting', 'sales'
            $table->string('module_name'); // e.g., 'CRM', 'Human Resources', 'Accounting'
            $table->enum('subscription_status', ['active', 'inactive', 'suspended', 'expired'])->default('active');
            $table->datetime('subscription_start')->nullable();
            $table->datetime('subscription_end')->nullable();
            $table->json('module_config')->nullable(); // For module-specific settings
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['brand_id', 'module_key']);
            $table->index('subscription_status');
            $table->unique(['brand_id', 'module_key']); // One subscription per brand per module
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('brand_module_subscriptions');
    }
};
