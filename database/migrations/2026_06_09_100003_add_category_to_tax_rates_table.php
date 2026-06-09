<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tax_rates', function (Blueprint $table) {
            $table->string('category', 30)->nullable()->after('name');
            $table->decimal('withholding_rate', 5, 2)->nullable()->after('rate');
            $table->string('tax_code', 10)->nullable()->after('withholding_rate');
        });
    }

    public function down(): void
    {
        Schema::table('tax_rates', function (Blueprint $table) {
            $table->dropColumn(['category', 'withholding_rate', 'tax_code']);
        });
    }
};
