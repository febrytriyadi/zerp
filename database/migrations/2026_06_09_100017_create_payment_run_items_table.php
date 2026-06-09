<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_run_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_type', 50);
            $table->bigInteger('invoice_id');
            $table->string('invoice_number', 100);
            $table->date('due_date');
            $table->decimal('original_amount', 20, 2);
            $table->decimal('outstanding_amount', 20, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 20, 2)->default(0);
            $table->decimal('payment_amount', 20, 2);
            $table->decimal('withholding_tax_amount', 20, 2)->default(0);
            $table->decimal('net_payment', 20, 2);
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_run_items');
    }
};
