<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name', 200);
            $table->string('contact_person', 200)->nullable();
            $table->string('phone', 50);
            $table->string('email', 100);
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
$table->foreignId('payment_term_id')->nullable()->constrained('payment_terms')->nullOnDelete();
$table->foreignId('chart_of_account_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
