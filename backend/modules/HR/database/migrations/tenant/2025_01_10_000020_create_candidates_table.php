<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('current_title')->nullable();
            $table->string('current_company')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('source')->nullable(); // direct, referral, agency, job_board
            $table->foreignId('referrer_id')->nullable()->constrained('hr_employees')->nullOnDelete();
            $table->json('tags')->nullable();
            $table->decimal('rating', 2, 1)->nullable(); // 1-5 stars
            $table->text('notes')->nullable();
            $table->boolean('blacklisted')->default(false);
            $table->string('blacklist_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('email');
            $table->index('blacklisted');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_candidates');
    }
};
