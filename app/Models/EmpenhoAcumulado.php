<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpenhoAcumulado extends Model
{
    use HasFactory;

    protected $fillable = ['empresa_id', 'valor_total', 'empenhos_list'];

    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class);
    }

    /**
     * Atualiza o acumulado para uma determinada empresa.
     */
    public static function updateAcumulado(int $empresaId)
    {
        $empresa = \App\Models\Empresa::with('empenhos')->find($empresaId);

        if (!$empresa) return;

        $total = $empresa->empenhos->sum('saldo');
        $lista = $empresa->empenhos->pluck('numero_empenho')->implode(', ');

        self::updateOrCreate(
            ['empresa_id' => $empresaId],
            [
                'valor_total' => $total,
                'empenhos_list' => $lista
            ]
        );
    }
}
