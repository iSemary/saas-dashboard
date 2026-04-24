<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('folders')->nullOnDelete()->comment('ID of the containing folder, null if in root');
            $table->string('hash_name')->comment('Hashed/encrypted name of the file in storage');
            $table->string('checksum')->comment('File checksum for integrity verification');
            $table->string('original_name')->comment('Original filename as uploaded');
            $table->string('mime_type')->comment('MIME type of the file');
            $table->string('host')->default('local')->comment('Storage host/provider (e.g., local, s3, etc.)');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active')->comment('Current status of the file (e.g., active, inactive, archived)');
            $table->enum('access_level', ['private', 'public'])->default('public')->comment('Access level/permissions for the file');
            $table->unsignedBigInteger('size')->comment('File size in bytes');
            $table->json('metadata')->nullable()->comment('Additional metadata as JSON');
            $table->boolean('is_encrypted')->default(false)->comment('Whether the file content is encrypted');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index('hash_name');
            $table->index('status');
            $table->index('access_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
