<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Mail\Tenant\EmployeePasswordResetMail;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Project\Project;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AttendanceController extends Controller
{
    public function __construct(AttendanceService $service)
    {
        activity()->disableLogging();

        $this->service = $service;
    }

    public function punchIn(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $status = resolve(StatusRepository::class)->attendanceApprove();

        DB::transaction(function() use($user, $request, $status) {
            $this->service
                ->setModel($user)
                ->setAttributes(array_merge($request->only('note', 'today'),['status_id' => $status, 'punch_in' => true]))
                ->validatePunchInDate($request->get('today'))
                ->punchIn();
        });

        return response()->json([
            'status' => true,
            'message' => __t('punched_in_successfully')
        ]);
    }

    public function APIpunchIn($request) // hassan boudgeau
    {
        /** @var User $user */
        $user = User::find($request['employee_id']);
//        Log::info($request);
        $request['status_id'] = 7;
        $request['punch_in'] = true;
        $request['note_user_id'] = $request['employee_id'];

//        $request=json_encode($request);

//        $status = resolve(StatusRepository::class)->attendanceApprove();
//        Log::info($status);

        DB::transaction(function() use($user, $request) {
            $this->service
                ->setModel($user)
                ->setAttributes($request)
                ->validatePunchInDate($request['today'])
                ->validateProjectAtt($request)
                ->punchIn();
        });
        Log::info(__t('punched_in_successfully'));

        return response()->json([
            'status' => true,
            'message' => __t('punched_in_successfully')
        ]);
    }

    public function punchOut(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $status = resolve(StatusRepository::class)->attendanceApprove();

        DB::transaction(
            function() use($user, $request, $status) {
                $this->service
                    ->setModel($user)
                    ->setAttributes(array_merge($request->only('note'),['status_id' => $status, 'punch_in' => false]))
                    ->punchOut();
            }
        );

        return response()->json([
            'status' => true,
            'message' => __t('punched_out_successfully')
        ]);
    }

    public function APIpunchOut($request)
    {
        /** @var User $user */
        $user = User::find($request['employee_id']);
//        Log::info($request);
        $request['status_id'] = 7;
        $request['punch_out'] = true;
        $request['note_user_id'] = $request['employee_id'];


//        $status = resolve(StatusRepository::class)->attendanceApprove();

        DB::transaction(
            function() use($user, $request) {
                $this->service
                    ->setModel($user)
                    ->setAttributes($request)
                    ->punchOut();
            }
        );
        Log::info(__t('punched_out_successfully'));

        return response()->json([
            'status' => true,
            'message' => __t('punched_out_successfully')
        ]);
    }
    public function fetch (Request $request) {

        $post_device = $request->device_id;
        $post_geofence = $request->geofence_id;
        $post_time = $request->time;
        $post_type = $request->type;
        $post_alert_id = $request->alert_id;
        $post_lat = $request->latitude;
        $post_lon = $request->longitude;
        $post_detail = $request->detail;
        $project = null;


        $today = Carbon::today()->toDateString();


        //log the incoming request
        $request = json_encode($request->all());
        Log::info($request);

        //get employees that has the posted device id (helmet) and active
        $employee_helmet = User::whereHas('helmets', function($q) use ($post_device) {
            $q->where('id', $post_device)->where('end_date',null);
        })->pluck('id');
        Log::info('employee helmet    '.$employee_helmet);


        //validate
        $previous_attendenace = Attendance::where('user_id',$employee_helmet)->whereDate('in_date', Carbon::today())->pluck('id');

        //check if this user has an attendance record for today
        if (count($previous_attendenace)>0) {
            Log::info('previous attendance record : '.$previous_attendenace);
            //validate time between past records and details and current request
            $previous_details = AttendanceDetails::where('attendance_id',$previous_attendenace)->whereDate('in_time', Carbon::today())->latest('in_time')->first();
            if($previous_details['in_time']){
                Log::info('previous details IN TIME: '.$previous_details['in_time']);
            }
//            $to = Carbon::now()->addHours(4);
            $to = Carbon::now();

            Log::info('current SERVER TIME: '.$to);
            if ($post_type === 'zone_in') {
                $diff_in_minutes = $to->diffInMinutes($previous_details['out_time']);
            }else {
                $diff_in_minutes = $to->diffInMinutes($previous_details['in_time']);
            }
            Log::info('time diff in minutes: '.$diff_in_minutes);

            if($diff_in_minutes  >= 15) {
                //catching the incorrect attendance, discarding stamps with less than x minutes difference
                Log::info('time difference is bigger than x=> 15');
                try {

                    //for testing regular attendance function punch in punch out here in this page
                    $reg_attendance = array();
                    try{
                        $project = Project::select('id')->where('geofence_id',$post_geofence)->first();
                        $reg_attendance['project_id'] = $project->id;

                    } catch (\Exception $exception) {
//                Log::info($project);
//                Log::error($exception);
                    }


                    Log::info($employee_helmet);
                    $reg_attendance['employee_id']= $employee_helmet[0];
                    $reg_attendance['note']='API test /  PROJECT NAME on VTS: '.$post_detail;

                    $reg_attendance['today']=$today;

                    if ($post_type === 'zone_in') {
                        $this->APIpunchIn($reg_attendance); //set the attendance record
                    } elseif ($post_type === 'zone_out') {
                        if (!$previous_details['out_time']) {
                                $this->APIpunchOut($reg_attendance); //set the attendance record
                        } else {
//                            $previous_details->out_time = Carbon::now()->addHours(4);
                            $previous_details->out_time = Carbon::now();

                            $previous_details->save();
                            Log::info('there is no empty out time, last out time has been updated');

                        }
                    }
                } catch (\Exception $exception) { /* Ignore */
                    Log::error("Helmet NO: -- ".$post_device." -- not found on the system, or because it is not matching the the  previous punch out after in or vice versa, check exception details below");
                    Log::info($exception);

                }
            } else {
                Log::info('time difference is less than x=> 15');
                //if the difference not 15 min then update last out time to now
//                $previous_details->out_time = Carbon::now()->addHours(4);
                $previous_details->out_time = Carbon::now();

                $previous_details->save();
                Log::info('in/out  time ignored, last out time has been updated');

            }

        } else {
            Log::info('this helmet has no entry today: PROCEED =>');
            try {

                //for testing regular attendance function punch in punch out here in this page
                $reg_attendance = array();
                try{
                    $project = Project::select('id')->where('geofence_id',$post_geofence)->first();
                    $reg_attendance['project_id'] = $project->id;

                } catch (\Exception $exception) {
//                Log::info($project);
//                Log::error($exception);
                }


                Log::info($employee_helmet);
                $reg_attendance['employee_id']= $employee_helmet[0];
                $reg_attendance['note']='API test /  PROJECT NAME on VTS: '.$post_detail;

                $reg_attendance['today']=$today;

                if ($post_type === 'zone_in') {
                    $this->APIpunchIn($reg_attendance); //set the attendance record
                } elseif ($post_type === 'zone_out') {
                    $this->APIpunchOut($reg_attendance); //set the attendance record

                }
            } catch (\Exception $exception) { /* Ignore */
                Log::error("Helmet NO: -- ".$post_device." -- not found on the system, or because it is not matching the the  previous punch out after in or vice versa, check exception details below");
                Log::info($exception);

            }
        }

        return $request;
    }

}
