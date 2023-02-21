<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preaviso extends Model
{

    use HasFactory;
    protected $fillable = [
        'fecha_envio',
        'funcionario_id',
        'fecha_inicio_contrato',
        'fecha_fin_contrato',
        'meses_duracion',
        'administrador_id'
    ];
}
