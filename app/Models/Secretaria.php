<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Secretaria extends Model
{
    use HasFactory;
    protected $fillable = ['nome'];

    public function processos()
    {
        return $this->hasMany(\App\Models\Processo::class);
    }

    public function empenhos()
    {
        return $this->hasMany(\App\Models\Empenho::class);
    }
}
