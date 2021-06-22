<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\ActiveTime;
use App\TimeAttest;

class GDPRCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:gdprcleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes persons who has logged in and then never accepted gdpr info';

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
     * @return int
     */
    public function handle()
    {
        logger("Running job for GDPR cleanup!");

        $amountdeleted = 0;
        $this->info("Looping through users...");
        $expDate = \Carbon\Carbon::now()->subDays(7);
        foreach(User::where('accepted_gdpr', false)->whereDate('updated_at', '<', $expDate)->get() as $user) {
            ActiveTime::where('user_id', $user->id)->delete();
            TimeAttest::where('user_id', $user->id)->delete();
            logger("Removing user ".$user->id." who hasn't accepted gdpr");
            $this->info("Deleted ".$user->name);
            $user->delete();
        }
    }
}
