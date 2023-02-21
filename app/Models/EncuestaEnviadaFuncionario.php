<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncuestaEnviadaFuncionario extends Model
{
    use HasFactory;
    protected $fillable = [
        'encuesta_id',
        'funcionario_id'
    ];
}
