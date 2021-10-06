<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChooseTrainingCoordinatorNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($project_time)
    {
        $this->project_time = $project_time;
    }

    public $project_time;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(_('Välj utbildningsanordnare för lektionstillfälle i Evikomp'))->view('emails.choose_training_coordinator');
    }
}
