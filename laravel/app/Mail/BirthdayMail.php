<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BirthdayMail extends Mailable
{
    use Queueable, SerializesModels;

    public $containt_email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($containt_email)
    {
        $this->containt_email = $containt_email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('backend.emails.birthday');
    }
}