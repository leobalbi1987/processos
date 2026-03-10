<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'cnpj'];

    public function empenhos()
    {
        return $this->hasMany(\App\Models\Empenho::class);
    }
}


