<?php

namespace App\Mail;

use App\Models\Empresa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class Preaviso extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $funcionario;

    public function __construct($funcionario)
    {
        $this->funcionario = $funcionario;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        setlocale(LC_ALL, 'es_ES');
        Carbon::setLocale('es');

        $empresa = Empresa::where('id',1)->get();
        $empresa=$empresa[0];
        $empleado = $this->funcionario;
        $duracion = Carbon::parse($this->funcionario->contratos[0]->fecha_inicio)->diffInMonths(Carbon::parse($this->funcionario->contratos[0]->fecha_fin));

        $inicio = Carbon::parse($this->funcionario->contratos[0]->fecha_inicio)->formatLocalized('%d de %B de %Y');
        $terminacion = Carbon::parse($this->funcionario->contratos[0]->fecha_fin)->formatLocalized('%d de %B de %Y');
        $pdf = PDF::loadView('pdfs.preaviso',  array('empleado' =>$this->funcionario,'empresa'=>$empresa,'duracion'=>$duracion,'inicio'=>$inicio,'terminacion'=>$terminacion));
        return $this->view('mails.preaviso', compact('empleado','empresa','inicio'))->attachData($pdf->output(), "Preaviso-Terminación-Contrato.pdf");
    }
}
