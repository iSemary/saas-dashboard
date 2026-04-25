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
        Schema::create('crm_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_event');
            $table->json('conditions');
            $table->json('actions');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index('trigger_event');
            $table->index(['is_active', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_automation_rules');
    }
};
