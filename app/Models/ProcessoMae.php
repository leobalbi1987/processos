<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcessoMae extends Model
{
    use HasFactory;
    protected $table = 'processos_mae';
    protected $fillable = [
        'numero_processo', 'validade_processo', 'objeto',
        'tipo_id', 'categoria_id', 'secretaria_id', 'empresa_id'
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
    public function processosPagamento()
    {
        return $this->hasMany(\App\Models\Processo::class, 'processo_mae_id');
    }
}
