<?php

namespace App\Exports;

use App\Filters\Tenant\AttendanceDailLogFilter;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Holiday\Holiday;
use App\Models\Tenant\Project\ProjectUser;
use App\Repositories\Core\Setting\SettingRepository;
use Carbon\Carbon;
//use http\Env\Request;
use Illuminate\Http\Request;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Tenant\Project\Project;


class ExportAttendanceLog implements FromArray, WithHeadings
{
    use Exportable;

    /**
     * @var int
     *
     */

    public function __construct(AttendanceDailLogFilter $filter, SettingRepository $repository)
    {
        $this->filter = $filter;
        $this->repository = $repository;
    }
    /**
     * @return object
     */
    public function collection()
    {
        $input = request()->all();
//        $input = json_encode($input);
        $date = $input['date'];


//        return ProjectUser::query()->select('user_id','project_id')->where('project_id', $this->id)->orderBy('project_id', 'DESC');
//        $rawAttendance =  Attendance::where('in_date',Carbon::today())->with('details','profile','user','details.project')->get();
        $rawAttendance =  Attendance::where('in_date',$date)->with([
            'details',
            'profile',
            'user',
            'details.project',
            'details.project.working_shifts',
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
            'user.past_projects.WorkingShifts.details'
        ])->get();


        Log::info('raw=> '.$rawAttendance);

//        $result = array_map(function ($value) {
//            return (array)$value;
//        }, $rawAttendance);
        Log::info('$rawAttendance '.json_encode($rawAttendance));
        $CalculatedAttendance = Project::calculateAttendanceDaily($rawAttendance);
        $CalculatedAttendance = json_decode(json_encode($CalculatedAttendance), true);


//        return DB::table('attendances')
//            ->select('p.employee_id','u.first_name','u.last_name','attendances.id','attendances.in_date','a.in_time','a.out_time')
//            ->join('attendance_details as a', 'a.attendance_id', '=', 'attendances.id')
//            ->join('users as u', 'u.id', '=', 'attendances.user_id')
//            ->join('profiles as p', 'p.user_id', '=', 'attendances.user_id')
//            ->orderBy('attendances.id', 'DESC');
//        Log::info($CalculatedAttendance);
        $additionalFields = [
            'assigned_pme_id' => null,
            'project_error' => null,
            'project_workshift_name' => null,
            'day_type' => null,
            'ws_time' => null,
            'calculated_wh_diff' => null
        ];

        $resultWithAdditionalFields = array_map(function($item) use ($additionalFields) {
            foreach ($additionalFields as $field => $value) {
                if (!isset($item[$field])) {
                    $item[$field] = $value;
                }
            }
            return $item;
        }, $CalculatedAttendance);

        $fieldOrder = [
            "id",
            "in_date",
            "user_id",
            'leave',
            'holiday',
            'holiday_name',
            'ams_project_id',
            'pme_id',
            'morning',
            'noon',
            'Project (predicted)',
            'lunch_break',
            'first_in_time',
            'last_out_time',
            'number_of_punches',
            'time_correction',
            'total',
            'employee_id',
            'full_name',
            'assigned_pme_id',
            'project_error',
            'project_workshift_name',
            'day_type',
            'ws_time',
            'calculated_wh_diff'

            // Add the rest of the fields in the desired order
        ];

        $data = array_map(function ($item) use ($fieldOrder) {
            $sortedItem = [];
            foreach ($fieldOrder as $field) {
                if (isset($item[$field])) {
                    $sortedItem[$field] = $item[$field];
                }
            }
            return $sortedItem;
        }, $resultWithAdditionalFields);
//        Log::info('date=> '.json_encode($data));
        return $data;




        // TODO: Implement query() method.
    }
    public function headings(): array
    {
//        return ["Attendance ID","Date", "User ID","Leave","Holiday","holiday_name","ams_project_id","pme_id","Morning","Noon","Actual Project (predicted)", "Lunch Break" ,"first_in_time","last_out_time","number_of_punches","Time Correction","Total Recorded Hours", "Employee ID", "Employee Name","assigned_pme_id","attendance_status","Workshift","Day Type","Workshift Base","Workshift Required","Total Hours" , "OverTime", "Extra Hours"];
        return [
            "Attendance ID",
            "Date",
            "User ID",
            'Employee ID',
            'Employee Name',
            'Leave',
            'Holiday',
            'Holiday Name',
            'ams_project_id',
            'PME (Projects Visited)',
            'PME Assigned (Project)',
            'Morning',
            'Noon',
            'Actual Project (predicted)',
            'Lunch Break',
            'first_in_time',
            'last_out_time',
            'number_of_punches',
            'Time Correction',
            'project_error',
            'Workshift',
            'Day Type',
            'Total Recorded',
            'Total Recorded Hours Rounded',
            'Workshift Base',
            'Workshift Required',
            'Total Hours',
            'OverTime',
            'Extra Hours'
        ];
    }


    /**
     * @return array
     */
    public function array(): array
    {
        // TODO: Implement array() method.
        $input = request()->all();
//        $input = json_encode($input);
        $date = $input['date'];


//        return ProjectUser::query()->select('user_id','project_id')->where('project_id', $this->id)->orderBy('project_id', 'DESC');
//        $rawAttendance =  Attendance::where('in_date',Carbon::today())->with('details','profile','user','details.project')->get();
        $rawAttendance =  Attendance::filters($this->filter)->where('in_date',$date)->with([
            'details',
            'profile',
            'user',
            'details.project',
            'details.project.working_shifts',
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
            'user.past_projects.WorkingShifts.details'
        ])->get();


//        Log::info('raw=> '.$rawAttendance);

//        $result = array_map(function ($value) {
//            return (array)$value;
//        }, $rawAttendance);
        $CalculatedAttendance = Project::calculateAttendanceDaily($rawAttendance);
        $CalculatedAttendance = json_decode(json_encode($CalculatedAttendance), true);


//        return DB::table('attendances')
//            ->select('p.employee_id','u.first_name','u.last_name','attendances.id','attendances.in_date','a.in_time','a.out_time')
//            ->join('attendance_details as a', 'a.attendance_id', '=', 'attendances.id')
//            ->join('users as u', 'u.id', '=', 'attendances.user_id')
//            ->join('profiles as p', 'p.user_id', '=', 'attendances.user_id')
//            ->orderBy('attendances.id', 'DESC');
//        Log::info($CalculatedAttendance);
        $additionalFields = [
            'assigned_pme_id' => null,
            'project_error' => null,
            'project_workshift_name' => null,
            'day_type' => null,
            'ws_base' => null,
            'ws_time' => null,
            'calculated_wh_diff' => null,
            'TotalHours' => null,
            'NormalOTHours' => null,
            'ExtraOTHours' => null
        ];

        $resultWithAdditionalFields = array_map(function($item) use ($additionalFields) {
            foreach ($additionalFields as $field => $value) {
                if (!isset($item[$field])) {
                    $item[$field] = $value;
                }
            }
            return $item;
        }, $CalculatedAttendance);

        $fieldOrder = [
            "id",
            "in_date",
            "user_id",
            'employee_id',
            'full_name',
            'leave',
            'holiday',
            'holiday_name',
            'ams_project_id',
            'pme_id',
            'assigned_pme_id',
            'morning',
            'noon',
            'Project (predicted)',
            'lunch_break',
            'first_in_time',
            'last_out_time',
            'number_of_punches',
            'time_correction',
            'project_error',
            'project_workshift_name',
            'day_type',
            'totalTimeNoRound',
            'total',
            'ws_base',
            'ws_time',
            'TotalHours',
            'NormalOTHours',
            'ExtraOTHours'

            // Add the rest of the fields in the desired order
        ];

        $data = array_map(function ($item) use ($fieldOrder) {
            $sortedItem = [];
            foreach ($fieldOrder as $field) {
                if (isset($item[$field])) {
                    $sortedItem[$field] = $item[$field];
                }
            }
            return $sortedItem;
        }, $resultWithAdditionalFields);
        Log::info('date=> '.json_encode($data));
        return $data;

    }
}
