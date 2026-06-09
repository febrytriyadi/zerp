<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('closing_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('branch_id')->constrained();
            $table->string('closing_number', 100)->unique();
            $table->string('closing_type', 50); // month_end, year_end, adjustment
            $table->foreignId('fiscal_period_id')->constrained();
            $table->string('description');
            $table->decimal('total_debit', 20, 2)->default(0);
            $table->decimal('total_credit', 20, 2)->default(0);
            $table->string('status', 20)->default('draft'); // draft, posted, voided
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closing_journals');
    }
};
