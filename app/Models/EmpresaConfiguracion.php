<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaConfiguracion extends Model
{
    protected $connection = 'tenant';

    protected $table = 'empresa_configuracion_pago';
    protected $guarded = ['id'];
    protected $casts = [
        'afecta_subsidio_transporte' => 'boolean',
        'pagar_vacaciones_31' => 'boolean'
    ];

    protected $columns = ['id', 'calculo_incapacidad_general'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    public function porcentaje()
    {
        return $this->belongsTo(PorcentageIncapacidad::class, 'calculo_incapacidad_general', 'id');
    }

    public function scopeExclude($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
}
