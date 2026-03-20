<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'empres-id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empres-id');
    }
}
