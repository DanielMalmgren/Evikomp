<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('custom:checklinks')->weekly();
        $schedule->command('custom:gdprcleanup')->daily()->environments(['prod']);
        $schedule->command('custom:attestreminder --forreal')->cron('0 7 * * 1,4')->environments(['prod']);
        $schedule->command('custom:attestremindersms --forreal')->cron('0 9 17-23 * 1')->environments(['prod']);
        $schedule->command('custom:presencereminder mode=sync --forreal')->cron('5 * * * *')->environments(['prod']);
        $schedule->command('custom:presencereminder mode=async --forreal')->cron('0 7 * * 2')->environments(['prod']);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
