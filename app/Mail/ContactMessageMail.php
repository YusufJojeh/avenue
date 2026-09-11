<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name: string, email: string, subject: string, message: string}  $data
     */
    public function __construct(public array $data)
    {
    }

    public function build(): self
    {
        return $this->subject('Contact form: '.$this->data['subject'])
            ->replyTo($this->data['email'], $this->data['name'])
            ->view('emails.contact');
    }
}
