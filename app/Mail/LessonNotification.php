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
    public function __construct($user, $lesson, $finished_lessons)
    {
        $this->user = $user;
        $this->lesson = $lesson;
        $this->finished_lessons = $finished_lessons;
    }

    public $user;
    public $lesson;
    public $finished_lessons;

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
