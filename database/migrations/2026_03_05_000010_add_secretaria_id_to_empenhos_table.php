<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->foreignId('secretaria_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('empenhos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('secretaria_id');
        });
    }
};
