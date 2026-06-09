<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->date('balance_date');
            $table->decimal('opening_balance', 20, 2);
            $table->decimal('total_debit', 20, 2)->default(0);
            $table->decimal('total_credit', 20, 2)->default(0);
            $table->decimal('ending_balance', 20, 2);
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->timestamps();
            $table->unique(['bank_account_id', 'balance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_account_balances');
    }
};
