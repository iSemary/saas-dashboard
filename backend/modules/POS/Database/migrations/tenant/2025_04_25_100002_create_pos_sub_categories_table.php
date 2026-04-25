<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pos_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('pos_categories')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_sub_categories');
    }
};
