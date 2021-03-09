<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nexmo\Laravel\Facade\Nexmo;

class NexmoSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:sms {number} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a sms to a number using Nexmo/Vonage';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $number = ltrim($this->argument('number'), '0');

        if(strpos($number, "+") !== 0) {
            $number = "+46".$number;
        }

        $this->info($number);

        try {
            Nexmo::message()->send([
                'to'   => $number,
                'from' => 'Evikomp',
                'text' => $this->argument('message')
            ]);
        } catch(\Vonage\Client\Exception\Request $e) {
            $this->info('Nexmo error:' . $e->getMessage());
        }
    }
}
