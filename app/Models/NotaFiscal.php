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
            if ($nota->valor_nf > $empenho->saldo) {
                throw ValidationException::withMessages(['valor_nf' => 'Saldo insuficiente no Empenho. Crie um novo empenho.']);
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

            $empenho = Empenho::find($nota->empenho_id);
            if ($empenho) {
                $empenho->saldo = $empenho->saldo - $nota->valor_nf;
                $empenho->save();
            }
        });
        static::updating(function (self $nota) {
            $originalValor = $nota->getOriginal('valor_nf');
            $originalEmpenhoId = $nota->getOriginal('empenho_id');
            $newEmpenhoId = $nota->empenho_id;
            $diff = $nota->valor_nf - $originalValor;
            if ($originalEmpenhoId == $newEmpenhoId) {
                if ($diff > 0) {
                    $empenho = Empenho::find($newEmpenhoId);
                    if (!$empenho || $diff > $empenho->saldo) {
                        throw ValidationException::withMessages(['valor_nf' => 'Saldo insuficiente no Empenho.']);
                    }
                }
            } else {
                $old = Empenho::find($originalEmpenhoId);
                if ($old) {
                    $old->saldo = $old->saldo + $originalValor;
                    $old->save();
                }
                $new = Empenho::find($newEmpenhoId);
                if (!$new) {
                    throw ValidationException::withMessages(['empenho_id' => 'Empenho inválido']);
                }
                if ($nota->valor_nf > $new->saldo) {
                    throw ValidationException::withMessages(['valor_nf' => 'Saldo insuficiente no novo Empenho.']);
                }
            }
        });
        static::updated(function (self $nota) {
            // Update Empenho's processo_id if this NF just got a process linked
            if ($nota->wasChanged('processo_id') && $nota->processo_id) {
                $empenho = $nota->empenho;
                if ($empenho) {
                    $empenho->processo_id = $nota->processo_id;
                    $empenho->save();
                }
            }

            $originalValor = $nota->getOriginal('valor_nf');
            $originalEmpenhoId = $nota->getOriginal('empenho_id');
            $newEmpenhoId = $nota->empenho_id;
            if ($originalEmpenhoId == $newEmpenhoId) {
                $diff = $nota->valor_nf - $originalValor;
                if ($diff != 0) {
                    $empenho = Empenho::find($newEmpenhoId);
                    if ($empenho) {
                        $empenho->saldo = $empenho->saldo - $diff;
                        $empenho->save();
                    }
                }
            } else {
                $new = Empenho::find($newEmpenhoId);
                if ($new) {
                    $new->saldo = $new->saldo - $nota->valor_nf;
                    $new->save();
                }
            }
        });
        static::deleted(function (self $nota) {
            $empenho = Empenho::find($nota->empenho_id);
            if ($empenho) {
                $empenho->saldo = $empenho->saldo + $nota->valor_nf;
                $empenho->save();
            }
        });
    }
}
