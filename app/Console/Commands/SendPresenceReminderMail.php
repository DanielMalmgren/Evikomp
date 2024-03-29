<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ProjectTime;
use App\User;

class SendPresenceReminderMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:presencereminder
                            {mode}
                            {--forreal : Really send the mails}
                            {onlysendto=all : Only send to this address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder to an administrator to confirm who was present on a teaching occurence';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        date_default_timezone_set('Europe/Stockholm');
        logger("Running job for sending presence reminders (".$this->argument('mode').")!");

        $this->info("Mode: ".$this->argument('mode'));

        $this->info("One hour ago: ".date('H:i', strtotime('-1 hour')));

        switch ($this->argument('mode')) {
            case "mode=sync": //Supposed to be run once every hour on all occurences that ended in the last hour
                $mode = "sync";
                $project_times = ProjectTime::where('date', date("Y-m-d"))
                                  ->where('endtime', '<=', date("H:i"))
                                  ->where('cancelled', false)
                                  ->where('endtime', '>', date('H:i', strtotime('-1 hour')))
                                  ->whereRaw('updated_at < timestamp(date, starttime)')
                                  ->get();
                break;
            case "mode=async": //Supposed to remind of all occurences that is still not updated (verified) after taking place
                $mode = "async";
                $project_times = ProjectTime::where('date', '<', date("Y-m-d"))
                                  ->whereRaw('updated_at < timestamp(date, starttime)')
                                  ->where('cancelled', false)
                                  ->where('date', '>', date('Y-m-d', strtotime('-1 month')))
                                  ->orderBy('date')
                                  ->get();
                break;
            default:
                $this->error("The mode argument needs to be either mode=sync or mode=async!");
                return;
        }

        $amountsent = 0;
        $amountfailed = 0;

        $adminlist = [];

        foreach($project_times as $key => $project_time) {
            if($project_time->users->count() == 1 &&
                $project_time->users->first()->id == $project_time->registered_by) {
                $project_times->forget((string)$key);
                continue;
            }
            $this->info($project_time->id.", ended: ".$project_time->date." ".$project_time->endtime.", updated:".$project_time->updated_at);
            foreach($project_time->workplace->workplace_admins as $user) {
                array_push($adminlist, $user->id);
            }
        }

        foreach(array_unique($adminlist) as $user_id) {
            $user = User::find($user_id);
            $this->info("Preparing email to ".$user->name." (".$user->email.")");
            $userpts = $project_times->whereIn('workplace_id', $user->admin_workplaces->pluck('id'));
            foreach($userpts as $project_time) {
                $this->info("Pt: ".$project_time->id);
            }
            if($this->option('forreal') || $this->argument('onlysendto')==$user->email) {
                $to = [];
                $to[] = ['email' => $user->email, 'name' => $user->name];

                try {
                    \Mail::to($to)->send(new \App\Mail\PresenceReminder($mode, $userpts));
                    $this->info("  Mail sent");
                    $amountsent++;
                } catch(\Swift_TransportException $e) {
                    $this->info("  Sending failed!");
                    logger("Couldn't send mail to ".$user->email);
                    $amountfailed++;
                }
            }
        }

        logger("Sending completed. ".$amountsent." presence reminders sent and ".$amountfailed." failed.");
    }
}
