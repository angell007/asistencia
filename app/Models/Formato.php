<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formato extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_formato_id',
        'logo',
        'firma',
        'encabezado',
        'cuerpo',
        'informacion',
        'responsable',
    ];
}
