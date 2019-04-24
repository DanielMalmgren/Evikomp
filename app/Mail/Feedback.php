<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Feedback extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content, $name, $email)
    {
        $this->content = $content;
        $this->name = $name;
        $this->email = $email;
    }

    public $content;
    public $name;
    public $email;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->$email, $this->$name)->view('emails.feedback');
    }
}
