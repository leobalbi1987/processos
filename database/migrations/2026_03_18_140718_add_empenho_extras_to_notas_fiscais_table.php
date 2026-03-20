<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notas_fiscais', function (Blueprint $table) {
            if (!Schema::hasColumn('notas_fiscais', 'empenho_extra_1_id')) {
                $table->foreignId('empenho_extra_1_id')->nullable()->constrained('empenhos')->onDelete('set null');
            }
            if (!Schema::hasColumn('notas_fiscais', 'empenho_extra_2_id')) {
                $table->foreignId('empenho_extra_2_id')->nullable()->constrained('empenhos')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notas_fiscais', function (Blueprint $table) {
            $table->dropForeign(['empenho_extra_1_id']);
            $table->dropForeign(['empenho_extra_2_id']);
            $table->dropColumn(['empenho_extra_1_id', 'empenho_extra_2_id']);
        });
    }
};
