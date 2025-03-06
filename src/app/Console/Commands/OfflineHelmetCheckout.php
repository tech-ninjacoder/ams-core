<?php

namespace App\Console\Commands;

use App\Models\Core\Auth\User;
use App\Models\Core\Notification\NotificationTemplate;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Notifications\Tenant\AttendanceNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class OfflineHelmetCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offline:fixhelmet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the attendance of the helmets went offline after it checking in';

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
        function date(){

        }
        $employee_helmet_offline = AttendanceDetails::where('in_time','<=', Carbon::now())->where('out_time',null)->get();
        //        $employee_helmet_offline = AttendanceDetails::where('out_time',null)->get();


        foreach ($employee_helmet_offline as $item) {

            $this->info('OFFLINE FIX: '.$item['id']);
            $attendance = Attendance::where('id', $item['attendance_id'])->get();
            foreach ($attendance as $att) {
                $att->status_id = 6;
                $att->save();
            }
            Log::info('OFFLINE FIX: '.$item['id']);
//            $item->out_time = Carbon::now();
            $item->status_id = 6;
            $date = new Carbon($item->in_time);
//            $date2 = $date->format('d/m/Y');
            $item->out_time = $date->endOfDay();
            $item->save();
            Log::info('OFFLINE FIX OUT TIME IS ENDOFDAY: '.$date);
        }
//
//
//        $user = User::first();
//        $database = NotificationTemplate::where('id',35)->get();
//        $details = AttendanceDetails::first();
//        Notification::send($user, new AttendanceNotification($database,'database',$details,$user));



        return 1;
    }
}
