<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Processo extends Model
{
    use HasFactory;

    public $nota_fiscal_id_to_link = null;

    protected $fillable = [
        'numero_processo', 'validade_processo', 'objeto',
        'tipo_id', 'categoria_id', 'secretaria_id', 'empresa_id', 'processo_mae_id',
        'localizacao', 'status_id', 'dias'
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

    public function status()
    {
        return $this->belongsTo(\App\Models\Status::class);
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

    public function empenhosPagos()
    {
        return $this->belongsToMany(Empenho::class, 'processo_empenhos', 'processo_id', 'empenho_id')
                    ->withPivot('valor_pago')
                    ->withTimestamps();
    }

    public function notaFiscal()
    {
        return $this->hasOne(\App\Models\NotaFiscal::class, 'processo_id');
    }

    public function statusHistoricos()
    {
        return $this->hasMany(\App\Models\ProcessoStatusHistorico::class);
    }

    protected static function booted()
    {
        static::creating(function ($processo) {
            // Se o número da nota fiscal foi enviado pelo formulário do Nova
            if (request()->has('nota_fiscal_numero')) {
                $nota = \App\Models\NotaFiscal::where('numero_nf', request('nota_fiscal_numero'))->first();
                if ($nota) {
                    $processo->nota_fiscal_id_to_link = $nota->id;
                }
            }
        });

        static::created(function ($processo) {
            // 1. Vincula a nota fiscal se o ID foi passado via URL (clicando em "Criar Processo" na Nota)
            $notaFiscalId = request()->query('nota_fiscal_id');

            // 2. Vincula a nota fiscal se o número foi digitado no formulário (passado pelo evento creating)
            if (!$notaFiscalId && isset($processo->nota_fiscal_id_to_link)) {
                $notaFiscalId = $processo->nota_fiscal_id_to_link;
            }

            if ($notaFiscalId) {
                $nota = \App\Models\NotaFiscal::find($notaFiscalId);
                if ($nota) {
                    $nota->processo_id = $processo->id;
                    $nota->save();

                    // IMPORTANTE: Recarregar o relacionamento para que o abaterSaldoEmpenhos() funcione
                    $processo->load('notaFiscal');
                }
            }

            // Abate saldo IMEDIATAMENTE após a criação
            $processo->abaterSaldoEmpenhos();
        });

        static::updating(function ($processo) {
            // Se o status mudou para "pago" (id 2) e ainda não houve abatimento
            if ($processo->isDirty('status_id') && $processo->status_id == 2) {
                // Verificamos se já existem empenhos pagos vinculados a este processo
                if ($processo->empenhosPagos()->count() == 0) {
                    $processo->abaterSaldoEmpenhos();
                }
            }
        });
    }

    /**
     * Lógica inteligente de abatimento de saldo
     */
    public function abaterSaldoEmpenhos()
    {
        Log::info('Iniciando abatimento de saldo para o processo: ' . $this->id);

        // Evita abater saldo se já houver registros de pagamento para este processo
        if ($this->empenhosPagos()->count() > 0) {
            Log::warning('Abatimento já realizado anteriormente para o processo: ' . $this->id);
            return;
        }

        $nota = $this->notaFiscal;
        if (!$nota) {
            Log::error('Nota Fiscal não encontrada para o processo: ' . $this->id);
            return;
        }

        Log::info('Nota Fiscal encontrada: ' . $nota->id . ' | Valor: ' . $nota->valor_nf);

        $valorRestante = $nota->valor_nf;

        // 1. Verifica se o usuário indicou empenhos específicos no processo (prioridade máxima)
        $empenhosIndicadosNoProcesso = $this->empenhosPagos()->get();

        if ($empenhosIndicadosNoProcesso->isNotEmpty()) {
            Log::info('Empenhos indicados no processo encontrados: ' . $empenhosIndicadosNoProcesso->pluck('id')->implode(', '));
            foreach ($empenhosIndicadosNoProcesso as $empenho) {
                if ($valorRestante <= 0) break;

                $abatimento = min($valorRestante, $empenho->saldo);
                Log::info("Abatendo R$ {$abatimento} do empenho {$empenho->id} (indicado no processo). Saldo anterior: {$empenho->saldo}");

                $empenho->saldo -= $abatimento;
                $empenho->processo_id = $this->id;
                $empenho->save();

                $this->empenhosPagos()->updateExistingPivot($empenho->id, ['valor_pago' => $abatimento]);
                $valorRestante -= $abatimento;
            }
        }

        // 2. Verifica os empenhos indicados na Nota Fiscal (Principal + Extras)
        if ($valorRestante > 0) {
            $empenhosDaNota = array_filter([$nota->empenho_id, $nota->empenho_extra_1_id, $nota->empenho_extra_2_id]);
            Log::info('Empenhos indicados na Nota Fiscal: ' . implode(', ', $empenhosDaNota));

            foreach ($empenhosDaNota as $empenhoId) {
                if ($valorRestante <= 0) break;

                $empenho = \App\Models\Empenho::find($empenhoId);
                // Abate se tiver saldo e ainda não tiver sido processado como "indicado no processo"
                if ($empenho && $empenho->saldo > 0 && !$empenhosIndicadosNoProcesso->contains('id', $empenho->id)) {
                    $abatimento = min($valorRestante, $empenho->saldo);
                    Log::info("Abatendo R$ {$abatimento} do empenho {$empenho->id} (indicado na nota). Saldo anterior: {$empenho->saldo}");

                    $empenho->saldo -= $abatimento;
                    $empenho->processo_id = $this->id;
                    $empenho->save();

                    $this->empenhosPagos()->syncWithoutDetaching([$empenho->id => ['valor_pago' => $abatimento]]);
                    $valorRestante -= $abatimento;
                }
            }
        }

        // 3. Lógica inteligente: busca outros empenhos da mesma empresa se ainda houver valor restante
        if ($valorRestante > 0) {
            Log::info("Valor restante para abater: R$ {$valorRestante}. Usando lógica inteligente em cascata.");
            $empenhoPrincipal = $nota->empenho;

            if ($empenhoPrincipal) {
                $outrosEmpenhos = \App\Models\Empenho::where('empresa_id', $empenhoPrincipal->empresa_id)
                    ->whereNotIn('id', array_filter([$nota->empenho_id, $nota->empenho_extra_1_id, $nota->empenho_extra_2_id]))
                    ->whereNotIn('id', $empenhosIndicadosNoProcesso->pluck('id'))
                    ->where('saldo', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->get();

                Log::info('Outros empenhos encontrados em cascata: ' . $outrosEmpenhos->pluck('id')->implode(', '));

                foreach ($outrosEmpenhos as $outro) {
                    if ($valorRestante <= 0) break;

                    $abatimento = min($valorRestante, $outro->saldo);
                    Log::info("Abatendo R$ {$abatimento} do empenho {$outro->id} (cascata automática). Saldo anterior: {$outro->saldo}");

                    $outro->saldo -= $abatimento;
                    $outro->processo_id = $this->id;
                    $outro->save();

                    $this->empenhosPagos()->syncWithoutDetaching([$outro->id => ['valor_pago' => $abatimento]]);
                    $valorRestante -= $abatimento;
                }
            }
        }
        Log::info('Fim do abatimento para o processo: ' . $this->id . ". Valor final restante: R$ {$valorRestante}");
    }


}
