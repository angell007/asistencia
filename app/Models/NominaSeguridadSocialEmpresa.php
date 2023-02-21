<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NominaSeguridadSocialEmpresa extends Model
{
    protected $connection = 'tenant';

    protected $table = 'nomina_seguridad_social_empresa';
    protected $guarded = ['id'];
}
