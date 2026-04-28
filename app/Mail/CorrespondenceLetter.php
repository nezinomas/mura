<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorrespondenceLetter extends Mailable
{
    use Queueable, SerializesModels;

    public $messageBody;
    public $senderEmail;

    public function __construct($messageBody, $senderEmail)
    {
        $this->messageBody = $messageBody;
        $this->senderEmail = $senderEmail;
    }

    public function build()
    {
        $mail = $this->subject('mura. Correspondence')
                     ->html($this->messageBody); // Send raw text directly

        if ($this->senderEmail) {
            $mail->replyTo($this->senderEmail);
        }

        return $mail;
    }
}