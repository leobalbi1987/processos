<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processos_mae', function (Blueprint $table) {
            $table->id();
            $table->string('numero_processo')->unique();
            $table->date('validade_processo')->nullable();
            $table->text('objeto')->nullable();
            $table->foreignId('tipo_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('secretaria_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('empresa_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processos_mae');
    }
};
