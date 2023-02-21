<?php

namespace App\Observers;

use App\Console\Commands\Renovacion;
use App\Models\Contrato;

class ContratoObserver
{
    /**
     * Handle the Contrato "created" event.
     *
     * @param  \App\Models\Contrato  $contrato
     * @return void
     */
    public function created(Contrato $contrato)
    {
        //
    }

    /**
     * Handle the Contrato "updated" event.
     *
     * @param  \App\Models\Contrato  $contrato
     * @return void
     */
    public function updated(Contrato $contrato)
    {
    }

    /**
     * Handle the Contrato "deleted" event.
     *
     * @param  \App\Models\Contrato  $contrato
     * @return void
     */
    public function deleted(Contrato $contrato)
    {
        //
    }

    /**
     * Handle the Contrato "restored" event.
     *
     * @param  \App\Models\Contrato  $contrato
     * @return void
     */
    public function restored(Contrato $contrato)
    {
        //
    }

    /**
     * Handle the Contrato "force deleted" event.
     *
     * @param  \App\Models\Contrato  $contrato
     * @return void
     */
    public function forceDeleted(Contrato $contrato)
    {
        //
    }
}
