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
                    'valor_nf' => "Saldo total insuficiente para esta empresa. O valor da nota (R$ " . number_format($nota->valor_nf, 2, ',', '.') . ") excede o saldo total acumulado de todos os empenhos (R$ {$saldoFormatado})."
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
            // Apenas validação básica de existência de empenho
            if ($nota->isDirty('empenho_id')) {
                $empenho = Empenho::find($nota->empenho_id);
                if (!$empenho) {
                    throw ValidationException::withMessages(['empenho_id' => 'Empenho inválido']);
                }
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
