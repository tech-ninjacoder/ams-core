<?php

namespace App\Console\Commands;

use App\Http\Controllers\Tenant\Employee\ManualAttendanceController;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Project\Project;
use App\Services\Tenant\Attendance\AttendanceService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class recurringAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ra:attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily recurring Attendance for none helmet employees v2';
    /**
     * @var AttendanceService
     */

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
        $auth_user = User::find(2);
        auth()->login($auth_user);

        $attendance_service = new AttendanceService();
        $attendance_service->setModel($auth_user);
        $settings = (object)$attendance_service->getSettingFromKeys('attendance')('recurring_attendance');
        Log::info('$settings===> '.json_encode($settings));

        $status_name = $attendance_service->autoApproval() ? 'approve' : 'pending';
        Log::info('status_name===> '.$status_name);
        auth()->logout();
        Log::info('recc '.$settings->recurring_attendance);

        if($settings->recurring_attendance == 1){ // Check if recurring setting is enabled
            $this->info('Recurring Attendance is Enabled, Proceed ');
            $projects = Project::whereHas('users', function($query) {
                $query->whereHas('status', function($query) {
                    $query->where('id', 1);  // Assuming 'id' 1 corresponds to 'on-duty' status
                })->whereHas('employmentStatus', function($query) {
                    $query->where('name', '!=', 'cancelled');
                })->whereDoesntHave('helmet')
                    ->whereHas('yesterdaysAttendance');
            })->with('working_shifts')
                ->with(['users' => function($query) {
                    $query->whereHas('status', function($query) {
                        $query->where('id', 1);  // Assuming 'id' 1 corresponds to 'on-duty' status
                    })->whereHas('employmentStatus', function($query) {
                        $query->where('name', '!=', 'cancelled');
                    })->whereDoesntHave('helmet')
                        ->whereHas('yesterdaysAttendance');
                }, 'users.status', 'users.employmentStatus', 'users.yesterdaysAttendance'])->get();

            foreach ($projects as $project) {
                $this->info('Checking Project '.$project->pme_id);

                foreach ($project->users as $user) {
                    $this->info('Checking User '.$user->name);

//                Log::info(json_encode($user->yesterdaysAttendance));
                    if (isset($user->yesterdaysAttendance) && $user->yesterdaysAttendance->isNotEmpty()) {
                        Log::info("Yesterday's Attendance exists");
                        $this->info("Yesterday's Attendance exists");

                        foreach ($user->yesterdaysAttendance as $attendance) {
                            Log::info(json_encode($attendance));
                            $details = AttendanceDetails::where('attendance_id',$attendance->id)->first();
                            Log::info('details '.json_encode($details));
                            $attendanceService = null;
                            if(!is_null($details)){
                                $auth_user = User::find(2);

                                if (!$auth_user) {
                                    $this->error('User not found.');
                                    return;
                                }

                                auth()->login($auth_user);
                                $attendance_today = Attendance::where('user_id',$user->id)->wheredate('in_date',Carbon::today()->toDateString())->get();
                                if(count($attendance_today)>0){
                                    $this->warn('Skip, alreay have attendance today.');

                                    Log::info('alreay have attendance today');
                                    Log::info('todays attendance: '.json_encode($attendance_today));
                                }else{


                                    $attendance_record = new Attendance();
                                    $attendance_record->in_date = Carbon::today()->toDateString();
                                    $attendance_record->user_id = $user->id;
                                    $attendance_record->status_id = 6;
                                    $attendance_record->working_shift_id = $attendance->working_shift_id;
                                    $attendance_record->behavior = 'regular';
                                    $attendance_record->source = 2; //recurring
                                    $attendance_record->hours_correction = 0;
                                    $attendance_record->minutes_correction = 0;
                                    $attendance_record->seconds_correction = 0;
                                    $attendance_record->tenant_id = 1;
                                    if($attendance_record->save()){
                                        $attendance_details = new AttendanceDetails();
                                        $attendance_details->in_time = Carbon::parse($details->in_time)->addDay();
                                        $attendance_details->out_time = Carbon::parse($details->out_time)->addDay();
                                        $attendance_details->attendance_id = $attendance_record->id;
                                        $attendance_details->status_id = 7;
                                        $attendance_details->project_id = $project->id;
                                        if($attendance_details->save()){
                                            $this->info('Attendance details created.');
                                        }else{
                                            $this->error('Attendance details was not created.');

                                        }
                                    }
                                }
                                auth()->logout();
                            }else{
                                Log::info('previous attendance details not found');
                                $this->error('Previous Attendance details was not fount.');

                            }


                        }
                    } else {
                        Log::info("Yesterday's Attendance does not exist");
                        $this->error('Yesterday\'s Attendance does not exist.');

                    }
                }
            }
        }else{
            $this->error('Recurring Attendance is disabled ');
        }

//        Log::info(json_encode($projects));
        return 0;
    }
}
