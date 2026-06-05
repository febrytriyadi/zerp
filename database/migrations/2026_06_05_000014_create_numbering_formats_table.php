<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numbering_formats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_type', 50);
            $table->string('format', 100);
            $table->string('prefix', 20);
            $table->integer('last_number')->default(0);
            $table->integer('next_number')->default(1);
            $table->string('last_year', 4)->nullable();
            $table->string('reset_period', 20)->default('yearly');
            $table->timestamps();

            $table->unique(['company_id', 'transaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numbering_formats');
    }
};
