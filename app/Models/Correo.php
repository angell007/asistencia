<?php
 
namespace App\Models;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class Correo extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The correo object instance.
     *
     * @var Correo
     */
    public $correo;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($correo)
    {
        $this->correo = $correo;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('aistencia@corvuslab.co')
                    ->view('mails.asistencia')
                    ->subject("Registro de Marcación Asistencia - GeneticApp")
                    ->text('mails.asistencia_plano');
                    
    }
}