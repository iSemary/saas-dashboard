<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sm_opt_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('sm_contacts')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('sm_campaigns')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->timestamp('opted_out_at');
            $table->timestamps();

            $table->index('contact_id');
            $table->index('campaign_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sm_opt_outs');
    }
};
