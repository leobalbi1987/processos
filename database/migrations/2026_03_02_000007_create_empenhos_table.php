<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empenhos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_empenho')->unique();
            $table->decimal('valor_global', 15, 2);
            $table->foreignId('processo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empenhos');
    }
};