<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sm_ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('variant_name');
            $table->text('body')->nullable();
            $table->unsignedTinyInteger('percentage')->default(50);
            $table->string('winner')->nullable();
            $table->json('stats')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sm_ab_tests');
    }
};
