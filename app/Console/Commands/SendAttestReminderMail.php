<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class SendAttestReminderMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:attestreminder
                            {--forreal : Really send the mails}
                            {onlysendto=all : Only send to this address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email with reminder to attest time to all users';

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
        logger("Running job for sending attest mail reminders!");
        $previous_month = date("m", strtotime("first day of previous month"));
        $previous_month_year = date("Y", strtotime("first day of previous month"));

        $amountsent = 0;
        $amountfailed = 0;
        $this->info("Looping through users...");
        foreach(User::whereNotNull('email')->get() as $user) {
            $last_month_is_attested = $user->month_is_fully_attested($previous_month_year, $previous_month, 0.9);
            $time_rows = $user->time_rows($previous_month_year, $previous_month);
            $time = end($time_rows)[32];
            if($last_month_is_attested || $time<1.0 || !isset($user->workplace) || !$user->workplace->send_attest_reminders) {
                continue;
            }

            setlocale(LC_TIME, $user->locale_id);
            $monthstr = strftime('%B', strtotime("first day of previous month"));

            $this->info("Preparing email to ".$user->name." (".$user->email.")");
            if($this->option('forreal') || $this->argument('onlysendto')==$user->email) {
                $to = [];
                $to[] = ['email' => $user->email, 'name' => $user->name];
                setlocale(LC_NUMERIC, $user->locale_id);

                try {
                    \Mail::to($to)->send(new \App\Mail\AttestReminder($time, $monthstr));
                    $this->info("  Mail sent");
                    $amountsent++;
                } catch(\Swift_TransportException $e) {
                    $this->info("  Sending failed!");
                    logger("Couldn't send mail to ".$user->email);
                    $amountfailed++;
                }
            }
        }
        logger("Sending cmpleted. ".$amountsent." reminders sent and ".$amountfailed." failed.");
    }
}
