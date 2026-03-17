<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Validation\ValidationException;

class NotaFiscalEmpenho extends Model
{
    protected $fillable = ['nota_fiscal_id', 'empenho_id', 'valor_abatido'];

    protected static function booted()
    {
        static::creating(function (self $rel) {
            $empenho = $rel->empenho;
            $nota = $rel->notaFiscal;

            if (!$empenho || $empenho->saldo <= 0) {
                throw ValidationException::withMessages(['empenho_id' => 'Este empenho não possui saldo disponível.']);
            }

            // Valida se o empenho é da mesma empresa que o empenho principal da nota
            if ($nota->empenho && $nota->empenho->empresa_id !== $empenho->empresa_id) {
                throw ValidationException::withMessages(['empenho_id' => 'Este empenho pertence a uma empresa diferente do empenho principal da nota.']);
            }

            // Se o valor_abatido não foi definido manualmente, calcula o máximo possível
            if (!$rel->valor_abatido) {
                $rel->valor_abatido = min($nota->saldo_restante_nota, $empenho->saldo);
            }

            if ($rel->valor_abatido > $empenho->saldo) {
                throw ValidationException::withMessages(['valor_abatido' => 'O valor excede o saldo disponível no empenho.']);
            }

            if ($rel->valor_abatido > $nota->saldo_restante_nota) {
                 // Permitimos abater apenas o necessário para completar a nota
                 $rel->valor_abatido = $nota->saldo_restante_nota;
            }
        });

        static::created(function (self $rel) {
            $empenho = $rel->empenho;
            $empenho->saldo -= $rel->valor_abatido;

            // Se a nota tiver um processo, vincula o empenho a ele também
            if ($rel->notaFiscal->processo_id) {
                $empenho->processo_id = $rel->notaFiscal->processo_id;
            }

            $empenho->save();
        });

        static::deleted(function (self $rel) {
            $empenho = $rel->empenho;
            $empenho->saldo += $rel->valor_abatido;
            $empenho->save();
        });
    }

    public function notaFiscal()
    {
        return $this->belongsTo(NotaFiscal::class);
    }

    public function empenho()
    {
        return $this->belongsTo(Empenho::class);
    }
}
