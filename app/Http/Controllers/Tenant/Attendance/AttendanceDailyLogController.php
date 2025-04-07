<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Exports\ExportAbsenceLog;
use App\Exports\ExportAttendaceTodayLog;
use App\Exports\ExportAttendanceLog;
use App\Exports\ExportProjectReport;
use App\Filters\Tenant\AttendanceDailLogFilter;
use App\Helpers\Traits\TenantAble;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Project\Project;
use App\Repositories\Core\Setting\SettingRepository;
use App\Repositories\Core\Status\StatusRepository;
//use AWS\CRT\Log;
//use AWS\CRT\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AttendanceDailyLogController extends Controller
{
    use TenantAble;

    protected SettingRepository $repository;

    public function __construct(AttendanceDailLogFilter $filter, SettingRepository $repository)
    {
        $this->filter = $filter;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();
        $date = $request->date;

        // Build the query to fetch attendance data with required relationships
        $reqo = Attendance::filters($this->filter)->with([
            'details.project.working_shifts',
            'details' => fn(HasMany $hasMany) => $hasMany
                ->select('id', 'in_time', 'out_time', 'attendance_id', 'status_id', 'review_by', 'added_by', 'attendance_details_id', 'project_id')
                ->orderByDesc('in_time'),
            'details.status:name,id',
            'details.project',
            'user.profile:*',
            'user:id,first_name,last_name,status_id',
            'user.department:id,name',
            'user.profilePicture',
            'user.projects',
            'user.past_projects' => function($query) use ($date) {
                $query->where('start_date', '<=', $date)
                    ->where(function($query) use ($date) {
                        $query->where('end_date', '>=', $date)
                            ->orWhereNull('end_date');
                    });
            },
            'user.past_projects.WorkingShifts',
            'user.past_projects.WorkingShifts.details',
            'user.status:id,name,class',
            'details.comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                ->select('id','commentable_type','commentable_id','user_id','type','comment', 'parent_id')
        ])
            ->latest();

        // Get paginated data based on 'per_page' query parameter
        $paginatedData = $reqo->paginate(request()->get('per_page', 15));

        // Calculate attendance and map it to the paginated collection
        $CalculatedAttendance = Project::calculateAttendanceDaily($paginatedData->getCollection());

        $arr = [];
        foreach($CalculatedAttendance as $cal => $val) {
            if (isset($val['id']) && is_numeric($val['id'])) {
                $arr[(int) $val['id']] = $val['total'];
            } else {
                Log::warning("Invalid ID or total value encountered: " . json_encode($val));
            }
        }

        // Add custom 'cal_total' field to each item in the paginated data collection
        $paginatedData->getCollection()->each(function ($item) use ($arr) {
            $item->cal_total = $arr[$item->id] ?? 0; // Default to 0 if no value found
        });

        // Structure the response to include custom fields and pagination
        $response = [
            'current_page' => $paginatedData->currentPage(),
            'data' => $paginatedData->getCollection(),
            'first_page_url' => $paginatedData->url(1),
            'from' => $paginatedData->firstItem(),
            'last_page' => $paginatedData->lastPage(),
            'last_page_url' => $paginatedData->url($paginatedData->lastPage()),
            'links' => $paginatedData->links(),
            'next_page_url' => $paginatedData->nextPageUrl(),
            'path' => $paginatedData->path(),
            'per_page' => $paginatedData->perPage(),
            'prev_page_url' => $paginatedData->previousPageUrl(),
            'to' => $paginatedData->lastItem(),
            'total' => $paginatedData->total(),
            'absence' => (new Attendance)->AbsenseCalc($date),  // Custom field
            'assigned' => (new Attendance)->Assigned($date),  // Custom field

        ];

        // Return the response as a JSON response
        return response()->json($response);
    }


    //TODO: find a way to send the project names for this user in one field
    public function ExcelExport() {
        $response = (new ExportAttendaceTodayLog($this->filter,$this->repository))->download('daily-log.csv');
        ob_end_clean();

        return $response;

    }
    public function ExcelDateExport(Request $request) {
        Log::info($request->date);
        $response = (new ExportAttendanceLog($this->filter,$this->repository))->download('daily-log.csv');
        ob_end_clean();

        return $response;

    }

    public function AbsenceExcelDateExport(Request $request)
    {
        // Validate the input date to ensure it's provided and properly formatted
        $validatedData = $request->validate([
            'date' => 'required|date',
        ]);

        try {
            // Generate and download the export
            return (new ExportAbsenceLog())->download('daily-absence-log-' . now()->format('Y-m-d') . '.csv');
        } catch (\Exception $e) {
            // Handle exceptions and return a user-friendly error message
            return response()->json([
                'message' => 'An error occurred while generating the absence log export.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function bulk_approve (Request $request){
        Log::info('request bulk approve=> '.json_encode($request->attendance_records));
        // Retrieve the array from the request
        $attendanceIds = $request->attendance_records;
        if($request->all_data){
            $attendance_date_rec = Attendance::find($attendanceIds[0]);
            $date = $attendance_date_rec->in_date;
            $updatedRows = Attendance::whereDate('in_date', $date)
                ->update(['status_id' => 7]);
            Log::info('$updatedRows=> '.$updatedRows);
            if ($updatedRows > 0) {
                return response()->json(['message' => 'All Attendance records updated successfully'], 200);
            } else {
                return response()->json(['message' => 'No attendance records were updated'], 200);
            }
        }else{
            // Update the status_id for the given array of attendance IDs
            $updatedRows = Attendance::whereIn('id', $attendanceIds)
                ->update(['status_id' => 7]);
            Log::info('$updatedRows=> '.$updatedRows);

            if ($updatedRows > 0) {
                return response()->json(['message' => 'Selected Attendance records updated successfully'], 200);
            } else {
                return response()->json(['message' => 'No attendance records were updated'], 200);
            }

        }

    }

    public function bulk_correct(Request $request){
        Log::info($request);
        $attendanceIds = $request->attendance_records;

        if(isset($request->total)){
            $date = $request->date;
//            $raw_attendance = Attendance::whereIn('id', $attendanceIds)->get();
            $raw_attendance = Attendance::filters($this->filter)->with([
                'details.project.working_shifts',
                'details' => fn(HasMany $hasMany) => $hasMany
                    ->select('id', 'in_time', 'out_time', 'attendance_id', 'status_id', 'review_by', 'added_by', 'attendance_details_id','project_id')
//                ->where('status_id', $attendanceApprove)
                    ->orderByDesc('in_time'),
                'details.status:name,id',
                'details.project',
                'user.profile:*',
                'user:id,first_name,last_name,status_id',
                'user.department:id,name',
                'user.profilePicture',
                'user.projects',
                'user.past_projects' => function($query) use ($date) {
                    $query->where('start_date', '<=', $date)
                        ->where(function($query) use ($date) {
                            $query->where('end_date', '>=', $date)
                                ->orWhereNull('end_date');
                        });
//                    ->orderBy('end_date', 'desc')
//                    ->first();
                },
                'user.past_projects.WorkingShifts',
                'user.past_projects.WorkingShifts.details',
                'user.status:id,name,class',


                'details.comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                    ->select('id','commentable_type','commentable_id','user_id','type','comment', 'parent_id')
            ])->whereIn('id', $attendanceIds)
                //            ->where('status_id', $attendanceApprove)
                ->latest();
            $paginatedData = $raw_attendance->paginate(request()->get('per_page', 100));


            $CalculatedAttendance = Project::calculateAttendanceDaily($paginatedData->getCollection()); // TODO: this data needs to ben sent and mapped to returned pagination

//            $CalculatedAttendance = Project::calculateAttendanceDaily($raw_attendance);
            Log::info('$CalculatedAttendance====> '.json_encode($CalculatedAttendance));
            foreach ($CalculatedAttendance as $attendance){
                Log::info('ID====> '.json_encode($attendance['id']));
                Log::info('total====> '.json_encode($attendance['total']));
                $attendancetimeInSeconds = Carbon::parse($attendance['total'])->diffInSeconds(Carbon::today());
                $current_attendance = Attendance::find($attendance['id']);
                $prev_correct_hours = $current_attendance->hours_correction;
                $prev_correct_minutes = $current_attendance->minutes_correction;
                $correctionInSeconds = $prev_correct_hours + $prev_correct_minutes;
                $attendancetimeInSeconds += $correctionInSeconds;


                $requested_total_seconds = $request->total * 60 * 60;
                Log::info('seconds====> '.json_encode($attendancetimeInSeconds));
                Log::info('$requested_total====> '.json_encode($requested_total_seconds));
                $attendance_diff_seconds = $requested_total_seconds - $attendancetimeInSeconds;
                Log::info('diff=====> '.$attendance_diff_seconds);

                if(($attendancetimeInSeconds + $attendance_diff_seconds) > 86399){
                    return response()->json(['message' => 'cannot add attendance more than 24 hours'], 400);

                }

                // Get the sign of the difference
                $sign = ($attendance_diff_seconds >= 0) ? 1 : -1;

// Take the absolute value of the difference for calculation
                $attendance_diff_seconds = abs($attendance_diff_seconds);

                $hours = floor($attendance_diff_seconds / 3600) * $sign; // Get the integer part as hours
                $remainingSeconds = $attendance_diff_seconds % 3600; // Get the remaining seconds
                $minutes = floor($remainingSeconds / 60)* $sign; // Get the integer part as minutes
                $seconds = $remainingSeconds % 60; // Get the remaining seconds


//                $hours = floor($attendance_diff_seconds / 3600); // Get the integer part as hours
//                $remainingSeconds = $attendance_diff_seconds % 3600; // Get the remaining seconds
//                $minutes = floor($remainingSeconds / 60); // Get the integer part as minutes
//                $seconds = $remainingSeconds % 60; // Get the remaining seconds

                Log::info('hoursx: '.$hours.' minutesx: '.$minutes.' secondsx: '.$seconds);


                $affected_attendance = Attendance::find($attendance['id']);
                $affected_attendance->hours_correction = $hours;
                $affected_attendance->minutes_correction = $minutes;
//                $affected_attendance->seconds_correction = $seconds;

                $affected_attendance->save();
            }

            return response()->json(['message' => 'requested total'], 200);
        }elseif (isset($request->hours) || isset($request->minutes)){
            $updatedRows = Attendance::whereIn('id', $attendanceIds)
                ->update([
                    'hours_correction' => $request->hours,
                    'minutes_correction' => $request->minutes
                ]);

            if ($updatedRows > 0) {
                return response()->json(['message' => 'Selected Attendance records correction updated successfully'], 200);
            } else {
                return response()->json(['message' => 'No attendance records correction were updated'], 200);
            }
        }else{
            return response()->json(['message' => 'missing data'], 400);
        }

    }
    public function clear_corrections(Request $request){
        $attendanceIds = $request->attendance_records;
        $attendances = Attendance::whereIn('id', $attendanceIds)
            ->update([
                'hours_correction' => 0,
                'minutes_correction' => 0
            ]);


        return response()->json(['message' => 'corrections are cleared'], 200);
    }

    public function hrms_upload(){
        Log::info('upload');
        $response_ = Artisan::call('hrms:attendance');
        Log::info('$response=> '.json_encode($response_));
        if(!is_null($response_)){
            return response()->json(['message' => 'uploaded successfully'], 200);

        }else{
            return response()->json(['message' => 'error'], 404);

        }


    }
}
