<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('pull:series')->twiceDaily(1, 13);
        $schedule->command('pull:matches-by-series-id')->twiceDaily(2, 14);
        $schedule->command('pull:upcoming-matches')->everyFifteenMinutes();
        $schedule->command('pull:recent-matches')->everyFifteenMinutes();
        $schedule->command('pull:live-matches')->everyMinute();
        $schedule->command('pull:match-info')->everyFiveMinutes();
        $schedule->command('pull:live-match-details')->everyFiveMinutes();
        $schedule->command('pull:news')->hourly();


        // clear expired token
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
        $schedule->command('users:delete-token')->daily();

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
