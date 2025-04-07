<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Services\Tenant\Attendance\AttendanceUpdateService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceUpdateController extends Controller
{
    use DepartmentAuthentications;

    public function __construct(AttendanceUpdateService $service)
    {
        $this->service = $service;
    }

    public function index(AttendanceDetails $attendanceDetails)
    {
        return $attendanceDetails->load([
            'comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id')
        ])->load('attendance');
    }

    public function request(Request $request, AttendanceDetails $attendanceDetails)
    {
        $attendanceDetails->load('attendance');

        $this->departmentAuthentications($attendanceDetails->attendance->user->id, true);
        $request->merge([ //boudgeau
            'out_time' => Carbon::parse($request->out_time)->addHours(4)->toDateTimeString(),
        ]); //boudgeau
        if($request->project_id){
            $attendanceDetails->project_id = $request->project_id;
            $attendanceDetails->save();
        }

        // Update the in_time field
        if ($request->has('in_time')) {
            $attendanceDetails->in_time = Carbon::parse($request->in_time)->addHours(4)->toDateTimeString();
            $attendanceDetails->save();
        }

        DB::transaction(
            fn () => $this->service
                ->setModel($attendanceDetails->attendance->user)
                ->setAttributes($request->only('project_id','in_time', 'out_time', 'reason', 'note'))
                ->mergeAttributes($this->service->getStatusAttribute())
                ->setDetails($attendanceDetails)
                ->validateIfAlreadyRequested($attendanceDetails->id)
                ->validateForRequest()
                ->validateIfNotFuture()
                ->validateAttendanceRequestDate()
                ->validateWorkShift()
                ->validateOwner()
                ->validateExistingPunchTime($attendanceDetails->id)
//                ->validateIfApproved()
//                ->duplicate($attendanceDetails->attendance)
                ->checkout($attendanceDetails->attendance)


        );

        return response()->json([
            'status' => true,
            'message' => __t('attendance_request_has_been_sent_successfully')
        ]);
    }
    public function correct(Request $request) //boudgeay
    {
        $attendance = Attendance::where('id',$request->attendance_id)->first();
        $attendance->hours_correction = $request->hours_correction;
        $attendance->minutes_correction = $request->minutes_correction;
        $attendance->save();


        return response()->json([
            'status' => true,
            'message' => __t('attendance_request_has_been_sent_successfully')
        ]);
    }
    public function checkout(Request $request, AttendanceDetails $attendanceDetails)
    {
        $attendanceDetails->load('attendance');

        $this->departmentAuthentications($attendanceDetails->attendance->user->id, true);
        $request->merge([ //boudgeau
            'out_time' => Carbon::parse($request->out_time)->addHours(3)->toDateTimeString(),
        ]); //boudgeau
//        Log::info($request);

        DB::transaction(
            fn () => $this->service
                ->setModel($attendanceDetails->attendance->user)
                ->setAttributes($request->only('in_time', 'out_time', 'reason', 'note'))
                ->mergeAttributes($this->service->getStatusAttribute())
                ->setDetails($attendanceDetails)
                ->validateIfAlreadyRequested($attendanceDetails->id)
//                ->validateForRequest() //deleted by hassan
                ->validateIfNotFuture()
                ->validateAttendanceRequestDate()
                ->validateWorkShift()
                ->validateOwner()
                ->validateExistingPunchTime($attendanceDetails->id)
                ->checkout($attendanceDetails->attendance)
//                ->validateIfApproved()
//                ->duplicate($attendanceDetails->attendance)

        );

        return response()->json([
            'status' => true,
            'message' => __t('attendance_request_has_been_sent_successfully')
        ]);
    }
}
