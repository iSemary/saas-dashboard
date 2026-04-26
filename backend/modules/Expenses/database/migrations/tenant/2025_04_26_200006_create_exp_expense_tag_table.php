<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exp_expense_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expense_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->unique(['expense_id', 'tag_id']);
            $table->foreign('expense_id')->references('id')->on('exp_expenses')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('exp_tags')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exp_expense_tag');
    }
};
