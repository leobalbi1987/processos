<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->decimal('saldo', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->dropColumn('saldo');
        });
    }
};
