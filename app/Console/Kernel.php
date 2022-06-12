<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ {
    Install,
    RefreshInstallation,
    AutorunCommand,
    CheckCustomerBirthday,
    UpdateAccountIdCommand
};
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Install::class,
        RefreshInstallation::class,
        AutorunCommand::class,
        UpdateAccountIdCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (env('APP_DEMO', false)) {
          $schedule->command('installation:refresh')->daily();
        } else {
			// $schedule->call(function () {
			// 	\Platform\Controllers\App\CronController::getProcessUsers();
			// })->everyFiveMinutes();

            $schedule->command('check:customer-birthday')->everyMinute();
		}
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
