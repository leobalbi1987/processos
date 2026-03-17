<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;

class NotaFiscal extends Model
{
    use HasFactory;
    protected $table = 'notas_fiscais';
    protected $fillable = ['numero_nf', 'ordem_de_servico', 'valor_nf', 'data_emissao', 'data_referencia', 'empenho_id', 'processo_id'];

    protected $casts = [
        'data_emissao' => 'date',
    ];

    public function processo()
    {
        return $this->belongsTo(\App\Models\Processo::class);
    }

    public function empenho()
    {
        return $this->belongsTo(Empenho::class, 'empenho_id');
    }

    public function processosPagamento()
    {
        return $this->hasMany(\App\Models\Processo::class, 'id', 'processo_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $nota) {
            $empenho = Empenho::find($nota->empenho_id);
            if (!$empenho) {
                throw ValidationException::withMessages(['empenho_id' => 'Empenho inválido']);
            }

            // Busca o saldo total acumulado de todos os empenhos da mesma empresa
            $saldoTotalEmpresa = Empenho::where('empresa_id', $empenho->empresa_id)->sum('saldo');

            if ($nota->valor_nf > $saldoTotalEmpresa) {
                $saldoFormatado = number_format($saldoTotalEmpresa, 2, ',', '.');
                throw ValidationException::withMessages([
                    'valor_nf' => "Saldo total insuficiente para esta empresa. Saldo disponível em todos os empenhos: R$ {$saldoFormatado}. Crie um novo empenho."
                ]);
            }
        });
        static::created(function (self $nota) {
            // Update Empenho's processo_id if this NF was created with a process linked
            if ($nota->processo_id) {
                $empenho = $nota->empenho;
                if ($empenho) {
                    $empenho->processo_id = $nota->processo_id;
                    $empenho->save();
                }
            }

            $valorRestante = $nota->valor_nf;
            $empenhoPrincipal = Empenho::find($nota->empenho_id);

            if ($empenhoPrincipal) {
                // Primeiro abate do empenho selecionado
                $abatimentoPrincipal = min($valorRestante, $empenhoPrincipal->saldo);
                $empenhoPrincipal->saldo -= $abatimentoPrincipal;
                $empenhoPrincipal->save();
                $valorRestante -= $abatimentoPrincipal;

                // Se ainda houver valor a abater, busca outros empenhos da mesma empresa com saldo
                if ($valorRestante > 0) {
                    $outrosEmpenhos = Empenho::where('empresa_id', $empenhoPrincipal->empresa_id)
                        ->where('id', '!=', $empenhoPrincipal->id)
                        ->where('saldo', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    foreach ($outrosEmpenhos as $outro) {
                        if ($valorRestante <= 0) break;

                        $abatimento = min($valorRestante, $outro->saldo);
                        $outro->saldo -= $abatimento;

                        // Se a nota tiver um processo, vincula os outros empenhos também
                        if ($nota->processo_id) {
                            $outro->processo_id = $nota->processo_id;
                        }

                        $outro->save();
                        $valorRestante -= $abatimento;
                    }
                }
            }
        });
        static::updating(function (self $nota) {
            if ($nota->isDirty('valor_nf')) {
                $originalValor = $nota->getOriginal('valor_nf');
                $novoValor = $nota->valor_nf;
                $diff = $novoValor - $originalValor;

                if ($diff > 0) {
                    // Se o valor aumentou, verifica o saldo total da empresa
                    $empenho = $nota->empenho;
                    $saldoTotalEmpresa = Empenho::where('empresa_id', $empenho->empresa_id)->sum('saldo');

                    if ($diff > $saldoTotalEmpresa) {
                        $saldoFormatado = number_format($saldoTotalEmpresa, 2, ',', '.');
                        throw ValidationException::withMessages([
                            'valor_nf' => "Saldo total insuficiente para cobrir o aumento do valor da nota. Saldo disponível em todos os empenhos: R$ {$saldoFormatado}."
                        ]);
                    }
                }
            }
        });
        static::updated(function (self $nota) {
            if ($nota->wasChanged('valor_nf')) {
                $originalValor = $nota->getOriginal('valor_nf');
                $novoValor = $nota->valor_nf;
                $diff = $novoValor - $originalValor;

                if ($diff > 0) {
                    // Se aumentou, abate o saldo extra seguindo a mesma lógica do create
                    $valorRestante = $diff;
                    $empenhoPrincipal = $nota->empenho;

                    if ($empenhoPrincipal) {
                        $abatimentoPrincipal = min($valorRestante, $empenhoPrincipal->saldo);
                        $empenhoPrincipal->saldo -= $abatimentoPrincipal;
                        $empenhoPrincipal->save();
                        $valorRestante -= $abatimentoPrincipal;

                        if ($valorRestante > 0) {
                            $outrosEmpenhos = Empenho::where('empresa_id', $empenhoPrincipal->empresa_id)
                                ->where('id', '!=', $empenhoPrincipal->id)
                                ->where('saldo', '>', 0)
                                ->orderBy('created_at', 'asc')
                                ->get();

                            foreach ($outrosEmpenhos as $outro) {
                                if ($valorRestante <= 0) break;
                                $abatimento = min($valorRestante, $outro->saldo);
                                $outro->saldo -= $abatimento;
                                $outro->save();
                                $valorRestante -= $abatimento;
                            }
                        }
                    }
                } else {
                    // Se diminuiu, devolve o saldo (simplificado: devolve para o empenho principal)
                    $empenhoPrincipal = $nota->empenho;
                    if ($empenhoPrincipal) {
                        $empenhoPrincipal->saldo += abs($diff);
                        $empenhoPrincipal->save();
                    }
                }
            }

            // Update Empenho's processo_id if this NF just got a process linked
            if ($nota->wasChanged('processo_id') && $nota->processo_id) {
                // Atualiza todos os empenhos que têm saldo < valor_global daquela empresa e estão vinculados a essa nota?
                // Na verdade, vamos atualizar o empenho principal e todos os outros que tiverem sido abatidos (mas não temos tabela pivot aqui para saber quais foram)
                // Por enquanto, vamos atualizar o empenho principal conforme a lógica original
                $empenho = $nota->empenho;
                if ($empenho) {
                    $empenho->processo_id = $nota->processo_id;
                    $empenho->save();
                }
            }
        });
        static::deleted(function (self $nota) {
            // Se a nota for deletada, devolve o valor para o empenho principal
            // (Como não temos pivot, devolvemos tudo para o principal por simplicidade)
            $empenho = $nota->empenho;
            if ($empenho) {
                $empenho->saldo += $nota->valor_nf;
                $empenho->save();
            }
        });
    }
}
