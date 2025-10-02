<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chargebacks', function (Blueprint $table) {
            $table->id();
            $table->string('chargeback_id')->unique()->comment('Internal chargeback identifier');
            $table->foreignId('transaction_id')->constrained('payment_transactions')->onDelete('restrict');
            $table->decimal('amount', 15, 2)->comment('Chargeback amount');
            $table->decimal('fee', 10, 2)->default(0)->comment('Chargeback fee');
            $table->string('reason_code')->nullable()->comment('Chargeback reason code');
            $table->text('reason_description')->nullable()->comment('Description of chargeback reason');
            $table->enum('status', ['received', 'under_review', 'accepted', 'disputed', 'won', 'lost', 'expired'])->default('received');
            $table->string('gateway_case_id')->nullable()->comment('Gateway-specific case ID');
            $table->date('evidence_due_date')->nullable()->comment('Deadline for submitting evidence');
            $table->json('evidence_submitted')->nullable()->comment('Evidence submitted for dispute');
            $table->enum('resolution', ['won', 'lost', 'pending'])->default('pending');
            $table->text('resolution_notes')->nullable()->comment('Notes about the resolution');
            $table->decimal('liability_shift_amount', 15, 2)->default(0)->comment('Amount covered by liability shift');
            $table->json('gateway_response')->nullable()->comment('Gateway response data');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('metadata')->nullable()->comment('Additional chargeback metadata');
            $table->timestamps();
            
            $table->index(['transaction_id', 'status']);
            $table->index(['status', 'evidence_due_date']);
            $table->index(['resolution', 'resolved_at']);
            $table->index(['gateway_case_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chargebacks');
    }
};
