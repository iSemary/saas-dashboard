<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_recipient_groups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('email_recipient_id');
            $table->bigInteger('email_group_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_recipient_groups');
    }
};
