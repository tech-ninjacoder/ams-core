<?php

namespace App\Console\Commands;

use App\Exports\ExportAttendaceTodayLog;
use App\Filters\Tenant\AttendanceDailLogFilter;
use App\Mail\Tenant\DailyLogEmail;
use App\Repositories\Core\Setting\SettingRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class email_daily_log extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily_log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Log to Email';

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
        // Resolve dependencies from the service container
        $filter = app(AttendanceDailLogFilter::class);
        $repository = app(SettingRepository::class);

        // Pass the dependencies to the constructor
        (new ExportAttendaceTodayLog($filter, $repository))->store('daily-log.csv');

//        (new ExportAttendaceTodayLog())->store('daily-log.csv');

//        return $response;

        $myEmail = env('daily_log_email_to');
        Mail::to($myEmail)->send(new DailyLogEmail());
        $this->info('Mail Send Successfully');

        return 0;
    }
}
