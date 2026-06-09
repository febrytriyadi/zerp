<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_id')->constrained()->cascadeOnDelete();
            $table->date('transaction_date');
            $table->string('description');
            $table->string('reference_number', 100)->nullable();
            $table->decimal('debit', 20, 2)->default(0);
            $table->decimal('credit', 20, 2)->default(0);
            $table->string('matching_status', 20)->default('unmatched');
            $table->string('matched_transaction_type', 50)->nullable();
            $table->bigInteger('matched_transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['matching_status', 'matched_transaction_type', 'matched_transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statement_lines');
    }
};
