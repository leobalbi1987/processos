<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_status_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('statuses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_status_historicos');
    }
};