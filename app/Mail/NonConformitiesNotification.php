<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NonConformitiesNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $valores;
    protected $time = 8;
    public $imagePath;
    public function __construct($valores)
    {
        //
        $this->valores = $valores;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $this->imagePath = ('https://sistemas.bailac.cl/compras4/img/logo_bailac.png'); // Path to the image

        return $this->view('correo.NonConformitiesNotification')->subject('No conformidad por Items con más de  '.$this->time.' días sin recepcionar')->with('valores', $this->valores);
    }
}
