<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LessonNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_name, $lesson_name)
    {
        $this->user_name = $user_name;
        $this->lesson_name = $lesson_name;
    }

    public $user_name;
    public $lesson_name;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(_('Notifiering om avklarad lektion'))->view('emails.lesson_notification');
    }
}