<?php

namespace App\Models;

use App\Http\Controllers\ContableIngresoController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonificacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'valor',
        'contable_ingreso_id',
        'contrato_id'
    ];

    public function ingreso()
    {
        return $this->belongsTo(ContableIngreso::class,  'contable_ingreso_id', 'id');
    }
    public function contableIngreso()
    {
        return $this->belongsTo(ContableIngreso::class);
    }

    public function scopeObtener($query, Funcionario $funcionario, $fechaInicio, $fechaFin)
    {
        $funcionario->load('contractultimate.bonificaciones.ingreso');
        $bonificaciones = $funcionario['contractultimate']['bonificaciones'];
        $data = [];
        foreach ($bonificaciones as  $bono) {
            array_push($data, (object)  $bono);
        }

        return  $funcionario['ingreso_prestacional'] = $data;
    }
}
