<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;

class NotaFiscal extends Model
{
    use HasFactory;
    protected $table = 'notas_fiscais';
    protected $fillable = ['numero_nf', 'ordem_de_servico', 'valor_nf', 'data_emissao', 'data_referencia', 'empenho_id', 'processo_id', 'empenho_extra_1_id', 'empenho_extra_2_id'];

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

    public function empenhoExtra1()
    {
        return $this->belongsTo(Empenho::class, 'empenho_extra_1_id');
    }

    public function empenhoExtra2()
    {
        return $this->belongsTo(Empenho::class, 'empenho_extra_2_id');
    }

    public function processosPagamento()
    {
        return $this->hasMany(\App\Models\Processo::class, 'id', 'processo_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $nota) {
            $empenhosIds = array_filter([$nota->empenho_id, $nota->empenho_extra_1_id, $nota->empenho_extra_2_id]);

            if (empty($empenhosIds)) {
                throw ValidationException::withMessages(['empenho_id' => 'Selecione pelo menos um empenho']);
            }

            $empenhoPrincipal = Empenho::find($nota->empenho_id);
            if (!$empenhoPrincipal) {
                throw ValidationException::withMessages(['empenho_id' => 'Empenho principal inválido']);
            }

            $somaSaldosSelecionados = Empenho::whereIn('id', $empenhosIds)->sum('saldo');
            $saldoTotalEmpresa = Empenho::where('empresa_id', $empenhoPrincipal->empresa_id)->sum('saldo');

            if ($nota->valor_nf > $somaSaldosSelecionados) {
                $somaFormatada = number_format($somaSaldosSelecionados, 2, ',', '.');
                $totalFormatado = number_format($saldoTotalEmpresa, 2, ',', '.');
                $faltaFormatado = number_format($nota->valor_nf - $somaSaldosSelecionados, 2, ',', '.');

                throw ValidationException::withMessages([
                    'valor_nf' => "A soma dos empenhos selecionados (R$ {$somaFormatada}) é insuficiente para cobrir o valor da nota (R$ " . number_format($nota->valor_nf, 2, ',', '.') . "). Faltam R$ {$faltaFormatado}. Selecione empenhos extras para completar o valor. Saldo total acumulado da empresa: R$ {$totalFormatado}."
                ]);
            }
        });

        static::created(function (self $nota) {
            // Sincroniza o processo_id se a nota já foi criada vinculada a um processo
            if ($nota->processo_id) {
                $empenho = $nota->empenho;
                if ($empenho) {
                    $empenho->processo_id = $nota->processo_id;
                    $empenho->save();
                }
            }
            // O abatimento de saldo NÃO ocorre mais aqui.
        });

        static::updating(function (self $nota) {
            $empenhosIds = array_filter([$nota->empenho_id, $nota->empenho_extra_1_id, $nota->empenho_extra_2_id]);

            if (empty($empenhosIds)) {
                throw ValidationException::withMessages(['empenho_id' => 'Selecione pelo menos um empenho']);
            }

            $empenhoPrincipal = Empenho::find($nota->empenho_id);
            if (!$empenhoPrincipal) {
                throw ValidationException::withMessages(['empenho_id' => 'Empenho principal inválido']);
            }

            $somaSaldosSelecionados = Empenho::whereIn('id', $empenhosIds)->sum('saldo');
            $saldoTotalEmpresa = Empenho::where('empresa_id', $empenhoPrincipal->empresa_id)->sum('saldo');

            if ($nota->valor_nf > $somaSaldosSelecionados) {
                $somaFormatada = number_format($somaSaldosSelecionados, 2, ',', '.');
                $totalFormatado = number_format($saldoTotalEmpresa, 2, ',', '.');
                $faltaFormatado = number_format($nota->valor_nf - $somaSaldosSelecionados, 2, ',', '.');

                throw ValidationException::withMessages([
                    'valor_nf' => "A soma dos empenhos selecionados (R$ {$somaFormatada}) é insuficiente para cobrir o valor da nota (R$ " . number_format($nota->valor_nf, 2, ',', '.') . "). Faltam R$ {$faltaFormatado}. Selecione empenhos extras para completar o valor. Saldo total acumulado da empresa: R$ {$totalFormatado}."
                ]);
            }
        });

        static::updated(function (self $nota) {
            // Sincroniza o processo_id se houver mudança
            if ($nota->wasChanged('processo_id') && $nota->processo_id) {
                $empenho = $nota->empenho;
                if ($empenho) {
                    $empenho->processo_id = $nota->processo_id;
                    $empenho->save();
                }
            }
        });

        static::deleted(function (self $nota) {
            // Não há mais necessidade de devolver saldo aqui, pois não foi abatido na nota
        });
    }
 }
