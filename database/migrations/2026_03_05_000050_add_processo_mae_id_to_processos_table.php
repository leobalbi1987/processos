<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->foreignId('processo_mae_id')->nullable()->constrained(table: 'processos_mae')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('processo_mae_id');
        });
    }
};
