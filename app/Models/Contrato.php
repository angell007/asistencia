<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;
    protected $connection = 'tenant';
    protected $fillable = [

        'funcionario_id',
        'centro_costo_id',
        'dependencia_id',
        'tipo_turno',
        'turno_fijo_id',
        'jefe_id',
        'fecha_inicio',
        'fecha_fin',
        'salario',
        'cargo_id',
        'tipo_contrato_id',
        'liquidado',
        'subsidio_transporte',
        'vacaciones_acumuladas'
    ];

    protected $appends = ['dias_restantes'];

    public function getDiasRestantesAttribute()
    {
        return Carbon::parse(Carbon::now())->diffInDays(Carbon::parse($this->fecha_fin));
    }

    // TODO: modificado para nuevo modelo  contrato  

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);;
    }
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class);;
    }
    public function tipo_contrato()
    {
        return $this->belongsTo(TipoContrato::class);;
    }

    public function turno_fijo()
    {
        return $this->belongsTo(TurnoFijo::class);;
    }

    public function bonificaciones()
    {
        return $this->hasMany(Bonificacion::class);
    }

    public function renovaciones()
    {
        return $this->hasMany(Renovacion::class);
    }
}
