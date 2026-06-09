<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('tax_invoice_number', 100)->unique();
            $table->date('tax_invoice_date');
            $table->string('transaction_type', 20);
            $table->foreignId('reference_id')->nullable();
            $table->string('reference_type', 100)->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('taxpayer_name', 200);
            $table->string('taxpayer_npwp', 30);
            $table->string('taxpayer_address', 500)->nullable();
            $table->decimal('dpp', 15, 2);
            $table->decimal('ppn_amount', 15, 2);
            $table->decimal('ppnbm_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['company_id', 'branch_id', 'tax_invoice_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_invoices');
    }
};
