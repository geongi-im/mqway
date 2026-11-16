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
        // 앱 푸시 자동 발송 규칙 (관리자 페이지 > 앱 푸시관리)
        // 발송이 1분을 넘겨도 다음 실행과 겹치지 않게 한다. 잠금은 15분 뒤 풀린다.
        $schedule->command('push:run-rules')
            ->everyMinute()
            ->withoutOverlapping(15);
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
