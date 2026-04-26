<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('em_unsubscribes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('em_contacts')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('em_campaigns')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->timestamp('unsubscribed_at');
            $table->timestamps();

            $table->index('contact_id');
            $table->index('campaign_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('em_unsubscribes');
    }
};
