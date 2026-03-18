<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoEmpenho extends Model
{
    protected $fillable = ['processo_id', 'empenho_id', 'valor_pago'];

    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }

    public function empenho()
    {
        return $this->belongsTo(Empenho::class);
    }
}
