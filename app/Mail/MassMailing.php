<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MassMailing extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body)
    {
        $this->subject = $subject;
        $this->body = $body;
    }

    public $subject;
    public $body;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = [];
        $from[] = ['email' => env('FEEDBACK_RECIPIENT_ADDRESS'), 'name' => env('FEEDBACK_RECIPIENT_NAME')];

        return $this->from($from)->subject($this->subject)->view('emails.freecontent');
    }
}
