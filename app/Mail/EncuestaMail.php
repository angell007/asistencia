<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EncuestaMail extends Mailable
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
        try {

            $date = Carbon::now()->format('Ymd');
            $doc = $this->funcionario->identidad;
            $base64 = base64_encode("$date,$doc");
            $base64url = strtr($base64, '+/=', '-_ ');
            $url = 'https://corvus.test/ae/' . $base64url;
            
            return $this->view('mails.encuesta', ['funcionario' => $this->funcionario, 'url' => $url]);
            
        } catch (\Exception $th) {
            dump($th->getMessage());
        }
    }
}
