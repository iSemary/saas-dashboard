<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('em_contact_list_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_list_id')->constrained('em_contact_lists')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('em_contacts')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['contact_list_id', 'contact_id']);
            $table->index('contact_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('em_contact_list_members');
    }
};
