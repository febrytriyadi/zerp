<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dunning_run_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dunning_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_type', 50);
            $table->bigInteger('invoice_id');
            $table->string('invoice_number', 100);
            $table->date('due_date');
            $table->integer('days_overdue');
            $table->decimal('original_amount', 20, 2);
            $table->decimal('outstanding_amount', 20, 2);
            $table->decimal('dunning_charge', 20, 2)->default(0);
            $table->decimal('total_due', 20, 2);
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dunning_run_items');
    }
};
