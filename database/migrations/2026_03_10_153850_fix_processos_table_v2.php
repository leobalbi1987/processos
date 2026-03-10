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
        Schema::table('processos', function (Blueprint $table) {
            if (!Schema::hasColumn('processos', 'status_id')) {
                $table->foreignId('status_id')->nullable()->after('categoria_id')->constrained('statuses')->nullOnDelete();
            }
            if (Schema::hasColumn('processos', 'situacao')) {
                $table->dropColumn('situacao');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            if (Schema::hasColumn('processos', 'status_id')) {
                $table->dropConstrainedForeignId('status_id');
            }
            if (!Schema::hasColumn('processos', 'situacao')) {
                $table->string('situacao')->nullable();
            }
        });
    }
};
