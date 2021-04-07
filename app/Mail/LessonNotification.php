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
    public function __construct($user_id, $user_firstname, $user_name, $lesson_name)
    {
        $this->user_id = $user_id;
        $this->user_firstname = $user_firstname;
        $this->user_name = $user_name;
        $this->lesson_name = $lesson_name;
    }

    public $user_id;
    public $user_firstname;
    public $user_name;
    public $lesson_name;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(_('Notifiering om avklarad modul'))->view('emails.lesson_notification');
    }
}
