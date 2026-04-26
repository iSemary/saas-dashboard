<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sm_import_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_list_id')->constrained('sm_contact_lists')->cascadeOnDelete();
            $table->string('file_path');
            $table->json('column_mapping')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->json('errors')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('contact_list_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sm_import_jobs');
    }
};
