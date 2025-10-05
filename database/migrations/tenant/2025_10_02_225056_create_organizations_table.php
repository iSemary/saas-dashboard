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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['company', 'enterprise', 'startup', 'ngo', 'research_institute', 'government'])->default('company');
            $table->string('industry')->nullable();
            $table->enum('size', ['startup', 'small', 'medium', 'large', 'enterprise'])->nullable();
            $table->string('website')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->json('social_links')->nullable(); // LinkedIn, Twitter, etc.
            $table->json('business_registration')->nullable(); // Registration number, tax ID, etc.
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active');
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->integer('employee_count')->nullable();
            $table->timestamp('founded_date')->nullable();
            $table->timestamps();

            $table->index(['type', 'industry']);
            $table->index(['country', 'state']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};