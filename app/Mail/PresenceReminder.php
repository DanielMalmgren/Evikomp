<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PresenceReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mode, $project_times)
    {
        $this->mode = $mode;
        $this->project_times = $project_times;
    }

    public $mode;
    public $project_times;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch ($mode) {
            case "sync":
                return $this->subject(_('Närvaro vid lärtillfälle'))->view('emails.presencereminder_sync');
                break;
            case "async":
                return $this->subject(_('Närvaro vid lärtillfälle'))->view('emails.presencereminder_async');
                break;
        }

    }
}
