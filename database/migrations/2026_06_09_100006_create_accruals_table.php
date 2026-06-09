<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accruals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->string('accrual_number', 100)->unique();
            $table->string('accrual_type', 50); // accrual, deferral
            $table->string('category', 50); // prepaid_expense, accrued_revenue, deferred_revenue, accrued_expense
            $table->string('description');
            $table->decimal('total_amount', 20, 2);
            $table->decimal('recognized_amount', 20, 2)->default(0);
            $table->decimal('remaining_amount', 20, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_periods'); // number of periods to spread over
            $table->integer('recognized_periods')->default(0);
            $table->decimal('amount_per_period', 20, 2);
            $table->foreignId('debit_account_id')->constrained('chart_of_accounts');
            $table->foreignId('credit_account_id')->constrained('chart_of_accounts');
            $table->string('status', 20)->default('active'); // active, fully_recognized, voided
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('voided_by')->nullable()->constrained('users');
            $table->timestamp('voided_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accruals');
    }
};
