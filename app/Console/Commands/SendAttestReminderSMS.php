<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Nexmo\Laravel\Facade\Nexmo;

class SendAttestReminderSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:attestremindersms
                            {--forreal : Really send the messages}
                            {onlyOnWeekday=all : Only send on this week day}
                            {onlysendto=all : Only send to this address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends sms with reminder to attest time to all users';

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
     * @return mixed
     */
    public function handle()
    {
        if($this->argument('onlyOnWeekday')!='onlyOnWeekday=all' && 
            substr($this->argument('onlyOnWeekday'), -1)!=\Carbon\Carbon::today()->dayOfWeek) {
            return;
        }
        logger("Running job for sending attest sms reminders!");
        $previous_month = (int)date("m", strtotime("first day of previous month"));
        $previous_month_year = (int)date("Y", strtotime("first day of previous month"));

        $amountsent = 0;
        $amountfailed = 0;
        $this->info("Looping through users...");
        foreach(User::whereNotNull('mobile')->get() as $user) {
            $last_month_is_attested = $user->month_is_fully_attested($previous_month_year, $previous_month, 1.9);
            $time_rows = $user->time_rows($previous_month_year, $previous_month);
            $time = end($time_rows)[32];
            if($last_month_is_attested || $time<1.0 || !isset($user->workplace) || !$user->workplace->send_attest_reminders) {
                continue;
            }

            setlocale(LC_TIME, $user->locale_id);
            $monthstr = strftime('%B', strtotime("first day of previous month"));

            $this->info("Preparing sms to ".$user->name." (".$user->mobile.")");
            if($this->option('forreal') || $this->argument('onlysendto')==$user->email) {
                $number = ltrim($user->mobile, '0');

                if(strpos($number, "+") !== 0) {
                    $number = "+46".$number;
                }
        
                $this->info($number);
        
                try {
                    Nexmo::message()->send([
                        'to'   => $number,
                        'from' => 'Evikomp',
                        'text' => "Hej. Vi kan se att du har ägnat ".$time." timmar åt projektet Evikomp under ".$monthstr.". Attestera dessa timmar här: ".env('APP_URL')."/attest MVH Evikomp"
                    ]);
                    $amountsent++;
                } catch(\Vonage\Client\Exception\Request $e) {
                    $this->info('Nexmo error:' . $e->getMessage());
                    $amountfailed++;
                }
            }
        }
        logger("Sending cmpleted. ".$amountsent." reminders sent and ".$amountfailed." failed.");
    }
}
