<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;
use App\Models\Birthday;

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
        if (App::environment('prod')){
          $schedule->command('backup:clean')->daily()->at('1:45');
          $schedule->command('backup:run')->daily()->at('2:00');
          $schedule->call(function () {
            Birthday::sentBirthdayMail();
          })->dailyAt('6:10');
          $schedule->call(function () {
            Birthday::sentMonthlyBirthdayMail();
          })->weeklyOn(1, '5:00');
          // ONLY FOR OVH
          $this->scheduleRunsHourly($schedule);
        }

        // $schedule->command('inspire')
        //          ->hourly();
    }

    // ONLY FOR OVH because we can not schedule every minute !
    protected function scheduleRunsHourly(Schedule $schedule)
    {
      foreach ($schedule->events() as $event) {
        $segments = explode(' ',$event->expression);
        $segments[0] = '*';
        $event->expression = implode(' ',$segments);
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