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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)
                ->unique()
                ->comment('ISO 4217 code for the currency (e.g., USD, EUR)');
            $table->string('name')
                ->comment('Full name of the currency (e.g., US Dollar)');
            $table->string('symbol')
                ->nullable()
                ->comment('Symbol used for the currency (e.g., $)');
            $table->unsignedTinyInteger('decimal_places')
                ->default(2)
                ->comment('Number of decimal places used for the currency');
            $table->decimal('exchange_rate', 10, 6)
                ->default(1)
                ->comment('Exchange rate relative to the base currency');
            $table->timestamp('exchange_rate_last_updated')
                ->nullable()
                ->comment('Timestamp when the exchange rate was last updated');
            $table->string('country')
                ->nullable()
                ->comment('Country or region where the currency is used');
            $table->enum('symbol_position', ['left', 'right'])
                ->default('left')
                ->comment('Position of the currency symbol (left or right)');
            $table->boolean('base_currency')
                ->default(false)
                ->comment('Indicates if this is the base currency');
            $table->unsignedInteger('priority')
                ->default(0)
                ->comment('Priority for ordering currencies in lists');
            $table->text('note')
                ->nullable()
                ->comment('Additional remarks or notes about the currency');
            $table->boolean('is_active')
                ->default(true)
                ->comment('Indicates if the currency is active');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
