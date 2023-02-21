<?php

namespace App\Mail;

use App\Admin;
use App\Models\Funcionario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $funcionario;
    /**
     * Create a new message instance.
     *
     * @return void
     */
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
        $funcionario =  $this->funcionario;
        return $this->view('mails.recover', compact('funcionario'));
    }
}
