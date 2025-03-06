<?php

namespace App\Console;

use App\Jobs\Tenant\AssignLeaveByStatusJob;
use App\Jobs\Tenant\AssignLeaveJob;
use App\Jobs\Tenant\AssignUpcomingWorkingShiftJob;
use App\Jobs\Tenant\RenewHolidayJob;
use App\Jobs\Tenant\UpdateWorkingShiftJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 */
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
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --queue=high,default --tries=2 --stop-when-empty')->everyMinute()->withoutOverlapping();

        $schedule->job(new UpdateWorkingShiftJob)->dailyAt('00:01');
        $schedule->job(new AssignUpcomingWorkingShiftJob())->dailyAt('00:01');
        $schedule->job(new AssignLeaveByStatusJob)->dailyAt('00:01');
        $schedule->job(new AssignLeaveJob)->monthly();
        $schedule->job(new RenewHolidayJob())->yearlyOn(12, 31, '22:00');
        $schedule->command('offline:fixhelmet')->dailyAt('20:01');
        $schedule->command('import:leaves')->dailyAt('20:30');
        $schedule->command('update:alert')->hourlyAt('30');

    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
