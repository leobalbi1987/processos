<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_fiscais', function (Blueprint $table) {
            $table->id();
            $table->string('numero_nf');
            $table->decimal('valor_nf', 15, 2);
            $table->date('data_emissao');
            $table->foreignId('empenho_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_fiscais');
    }
};