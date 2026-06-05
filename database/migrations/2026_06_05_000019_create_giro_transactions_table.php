<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('giro_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10);
            $table->string('transaction_number', 50);
            $table->string('giro_number', 100);
            $table->date('transaction_date');
            $table->date('due_date');
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('issuer_name', 200);
            $table->foreignId('chart_of_account_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('status', 20);
            $table->timestamp('cleared_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giro_transactions');
    }
};
