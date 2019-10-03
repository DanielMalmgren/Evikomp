<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Feedback extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content, $lesson, $name, $email, $mobile, $workplace)
    {
        $this->content = $content;
        $this->lesson = $lesson;
        $this->name = $name;
        $this->email = $email;
        $this->mobile = $mobile;
        $this->workplace = $workplace;
    }

    public $content;
    public $lesson;
    public $name;
    public $email;
    public $mobile;
    public $workplace;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->email, $this->name)->view('emails.feedback');
    }
}
