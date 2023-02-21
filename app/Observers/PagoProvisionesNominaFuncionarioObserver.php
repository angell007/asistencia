<?php

namespace App\Observers;

use App\Models\Contrato;
use App\Models\PagoProvisionesNominaFuncionario;

class PagoProvisionesNominaFuncionarioObserver
{
    public function saving(PagoProvisionesNominaFuncionario $PagoProvisionesNominaFuncionario)
    {
        $contrato = Contrato::where('funcionario_id', $PagoProvisionesNominaFuncionario->funcionario_id)
            ->where('liquidado', 0)->first();

        if ($contrato != null) {
            $contrato->update([
                'vacaciones_acumuladas' => (float) $contrato->vacaciones_acumuladas + (float)$PagoProvisionesNominaFuncionario->dias_acumulados_vacaciones
            ]);
        }
    }
}
