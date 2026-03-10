<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empenho extends Model
{
    use HasFactory;
    protected $fillable = ['numero_empenho', 'valor_global', 'saldo', 'empresa_id', 'secretaria_id', 'processo_id'];
    protected static function booted(): void
    {
        static::creating(function (self $empenho) {
            if (is_null($empenho->saldo)) {
                $empenho->saldo = $empenho->valor_global;
            }
        });
    }

    public function processo()
    {
        return $this->belongsTo(\App\Models\Processo::class);
    }

    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }
    public function secretaria()
    {
        return $this->belongsTo(\App\Models\Secretaria::class);
    }
    public function notasFiscais()
    {
        return $this->hasMany(\App\Models\NotaFiscal::class);
    }

    public function processosPagamento()
    {
        return $this->hasManyThrough(
            \App\Models\Processo::class,
            \App\Models\NotaFiscal::class,
            'empenho_id', // Foreign key on notas_fiscais table
            'id',         // Foreign key on processos table
            'id',         // Local key on empenhos table
            'processo_id' // Local key on notas_fiscais table
        );
    }


}
