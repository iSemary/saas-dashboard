<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('em_campaign_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('em_campaigns')->cascadeOnDelete();
            $table->foreignId('contact_list_id')->constrained('em_contact_lists')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'contact_list_id']);
            $table->index('contact_list_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('em_campaign_lists');
    }
};
