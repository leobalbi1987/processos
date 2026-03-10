<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcessoStatusHistorico extends Model
{
    use HasFactory;
    protected $fillable = [
        'processo_id', 'status_id', 'user_id', 'observacao'
    ];
    
    public function processo()
    {
        return $this->belongsTo(\App\Models\Processo::class);
    }


    public function status()
    {
        return $this->belongsTo(\App\Models\Status::class);
    }   

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
        

}
