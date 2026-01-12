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
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('domains:getInfo')->everyFifteenMinutes()->name('updateInfo')->appendOutputTo(storage_path('logs/caching.log'))->withoutOverlapping();
        $schedule->command('domainsuploaded:check')->hourly()->name('checkuploaded')->appendOutputTo(storage_path('logs/caching.log'))->withoutOverlapping();
        $schedule->command('namecheap:checknew')->everyMinute()->name('checkDomains')->appendOutputTo(storage_path('logs/caching.log'))->withoutOverlapping();
        $schedule->call('App\Parsing\ParsingStart@parsing')->dailyAt('00:00')->name('parsing')->appendOutputTo(storage_path('logs/caching.log'))->withoutOverlapping();
        $schedule->command('domainsuploaded:check')->everyMinute()->name('checkFromUploaded')->appendOutputTo(storage_path('logs/caching.log'))->withoutOverlapping();
        $schedule->command('cache:clear')->daily()->name('cacheClear')->appendOutputTo(storage_path('logs/caching.log'))->withoutOverlapping();
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
