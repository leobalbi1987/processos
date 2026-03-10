<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Processo extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_processo', 'validade_processo', 'objeto',
        'tipo_id', 'categoria_id', 'secretaria_id', 'empresa_id', 'processo_mae_id',
        'localizacao', 'situacao', 'dias'
    ];
    protected $casts = [
        'validade_processo' => 'date',
    ];

    public function tipo()
{
   return $this->belongsTo(\App\Models\Tipo::class);
}

    public function categoria()
    {
        return $this->belongsTo(\App\Models\Categoria::class);
    }

    public function secretaria()
    {
        return $this->belongsTo(\App\Models\Secretaria::class);
    }
    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }
    public function processoMae()
    {
        return $this->belongsTo(\App\Models\ProcessoMae::class, 'processo_mae_id');
    }

    public function empenhos()
    {
        return $this->hasMany(\App\Models\Empenho::class);
    }
    public function notaFiscal()
    {
        return $this->hasOne(\App\Models\NotaFiscal::class);
    }

    public function statusHistoricos()
    {
        return $this->hasMany(\App\Models\ProcessoStatusHistorico::class);
    }

    protected static function booted()
    {
        static::created(function ($processo) {
            // Verifica se o ID da nota fiscal foi passado via URL (através da requisição do Nova)
            $notaFiscalId = request()->query('nota_fiscal_id');

            if ($notaFiscalId) {
                $nota = \App\Models\NotaFiscal::find($notaFiscalId);
                if ($nota) {
                    $nota->processo_id = $processo->id;
                    $nota->save();
                }
            }
        });
    }


}
