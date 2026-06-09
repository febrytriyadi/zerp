<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('closing_journals', function (Blueprint $table) {
            $table->json('items')->after('journal_entry_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('closing_journals', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }
};
