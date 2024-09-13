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
        $schedule->command('cron:reminder-survailant-user')
            ->appendOutputTo(storage_path('logs/laravel-scheduller-stdout.log'))
            ->dailyAt('08:00');
        $schedule->command('cron:reminder-survailant-internal')
            ->appendOutputTo(storage_path('logs/laravel-scheduller-stdout.log'))
            ->mondays()->at("09:00");
        $schedule->command('cron:reminder-invoice-expired-finance')
            ->appendOutputTo(storage_path('logs/laravel-scheduller-stdout.log'))
            ->dailyAt('10:00');
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
