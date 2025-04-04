<?php

namespace App\Models\Tenant\Project;

use App\Helpers\Traits\DateRangeHelper;
use App\Http\Controllers\Tenant\Project\ProjectUserController;
use App\Models\Core\Status;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Holiday\Holiday;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Project\Relationship\ProjectRelationShip;
use App\Models\Tenant\TenantModel;
use App\Services\Tenant\Project\ProjectService;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Collection;


class Project extends TenantModel
{
    use ProjectRelationShip, SoftDeletes;

    protected $fillable = [
        'name','pme_id', 'description', 'location','location_id','subdivision_id', 'geometry', 'status_id', 'p_start_date', 'p_end_date', 'est_man_hour', 'lunch_in', 'contractor_id', 'type','sync_date'
    ];
    protected $casts = ['pme_id' => 'string'];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($project) {
            // Fetch the "completed" status ID dynamically
            $completedStatusId = Status::where('type', 'project')
                ->where('name', 'status_completed')
                ->value('id');

            if ($project->isDirty('status_id')) {
                $originalStatusId = $project->getOriginal('status_id');

                if ($project->status_id == $completedStatusId) {
                    // If the new status is "completed"
                    $project->handleStatusCompleted();
                } elseif ($originalStatusId == $completedStatusId) {
                    // If the old status was "completed" and it changed to something else
                    $project->handleStatusNotCompleted();
                }
            }
        });
    }


//    // Define the custom function to handle the status change
//    public function handleStatusCompleted()
//    {
//        // Perform  actions when status is changed to "completed"
//        // Action 1: Release Employees assigned
//        Log::info("Project ID {$this->id} has been marked as completed.");
//        $project = Project::find($this->id);
//        $userIds = $project->users()->pluck('id')->toArray();
//        Log::info('Project user IDs: ' . json_encode($userIds));
//        ProjectUser::whereNull('end_date')
//            ->where('project_id', $this->id)
//            ->whereIn('user_id', $userIds)
//            ->update([
//                'end_date' => nowFromApp()->format('Y-m-d')
//            ]);
//        // Action 2: Remove Geofence from tracking system
//        $this->deleteGeofence($this->id);
//
//
//    }

    public function handleStatusCompleted()
    {
        try {
            Log::info("Project ID {$this->id} has been marked as completed.");

            // Find project and validate existence
            $project = Project::find($this->id);
            if (!$project) {
                Log::error("Project ID {$this->id} not found.");
                return;
            }

            // Release employees assigned
            $userIds = $project->users()->pluck('id')->toArray();
            Log::info('Project user IDs: ' . json_encode($userIds));

            DB::transaction(function () use ($userIds) {
                ProjectUser::whereNull('end_date')
                    ->where('project_id', $this->id)
                    ->whereIn('user_id', $userIds)
                    ->update([
                        'end_date' => nowFromApp()->format('Y-m-d')
                    ]);
            });

            // Remove geofence from tracking system
            try {
                if (method_exists($this, 'deleteGeofence')) {
                    $response = $this->deleteGeofence($project->geofence_id);
                    Log::info("Geofence delete response for Project ID {$this->id}: " . json_encode($response));

                    // Validate response
                    if (isset($response['status']) && $response['status'] === 'success' && isset($response['data']['status']) && $response['data']['status'] === 1) {
                        // Clear geofence ID
                        $project->geofence_id = null;
                        $project->save();
                        Log::info("Geofence ID cleared for Project ID {$this->id}.");
                    } else {
                        Log::warning("Geofence deletion failed for Project ID {$this->id}: " . json_encode($response));
                    }
                } else {
                    Log::warning("deleteGeofence method not defined for Project ID {$this->id}.");
                }
            } catch (\Exception $e) {
                Log::error("Failed to delete geofence for Project ID {$this->id}: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            Log::error("Error in handleStatusCompleted for Project ID {$this->id}: " . $e->getMessage());
        }
    }

    public function handleStatusNotCompleted()
    {
        // Get the Project instance
        $project = Project::find($this->id);

        // Instantiate the controller using the app() helper
        $controller = app(ProjectUserController::class);

        // Call the GeometryToGeofence method and pass the project instance
        $controller->GeometryToGeofence($project);
        $geofence_id = $controller->GetGeofenceOfProject($project->pme_id);
        Log::info('$geofence_id ===> '.$geofence_id);
        $project->geofence_id = $geofence_id;
        $project->save();
        Artisan::call('update:alert');

    }





    public static function project_mh($id){
//        $bb = AttendanceDetails::where('project_id',$id)->sum(DB::raw("TIME_TO_SEC(TIMEDIFF(out_time,in_time))"));
        $hours = AttendanceDetails::where('project_id',$id)->selectRaw(DB::raw('CAST(SUM(TIME_TO_SEC(TIMEDIFF(out_time, in_time))) AS SIGNED) AS worked'))->get();
        $worked = $hours->sum('worked');

        return self::convertSecondsToHoursMinutes($worked);
    }

    private static function convertSecondsToHoursMinutes($seconds): string
    {
        $minutes = $seconds / 60;

        $hours = (int)($minutes / 60);

        $restMinutes = abs($minutes % 60);
        $restSecond = abs($seconds % 60);

        $restMinutes = strlen((string) $restMinutes) === 1 ? "0".$restMinutes : $restMinutes;
        $restSecond = strlen((string) $restSecond) === 1 ? "0".$restSecond : $restSecond;

        return $hours.":".$restMinutes.":".$restSecond;

    }
    private static function eliminateSeconds($total_seconds){
        // Calculate total seconds
//        $total_seconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        // Round down the total seconds
        $rounded_seconds = floor($total_seconds);

        // Calculate hours and minutes from rounded seconds
        $hours = floor($rounded_seconds / 3600);
        $minutes = floor(($rounded_seconds % 3600) / 60);
        $reduced = ($hours*3600)+($minutes*60);
        Log::info('brefore reduction seconds: '.$total_seconds);
        Log::info('reduced hours: '.$hours);
        Log::info('reduced minutes: '.$minutes);
        Log::info('total seconds reduced: '.$reduced);


        return $reduced;
    }
    public static function calculateAttendanceDaily ($attendance) :object {
//        Log::info('ateee===>'.json_encode($attendance));
//        Log::info($attendance);


        //get the holiday and attache it
        $attendance->each(function ($attendance_source) {
            $inDate = Carbon::parse($attendance_source->in_date);
            $holiday = Holiday::whereMonth('start_date', '=', $inDate->month)
                ->whereDay('start_date', '<=', $inDate->day)
                ->whereMonth('end_date', '=', $inDate->month)
                ->whereDay('end_date', '>=', $inDate->day)
                ->first();
            if ($holiday) {
                $attendance_source->holiday_name = $holiday->name;
            } else {
                $attendance_source->holiday_name = null; // Set to null if no matching holiday
            }
        });
        //end get the holiday and attache it

        if(!is_array($attendance)){
            $attendance = $attendance->toArray();
        }
//        Log::info($attendance);
        $i = 0;
        $attCalc = [];
        $hours_correction = 0;
        $minutes_correction = 0;
        $date = null;
        foreach ($attendance as $key => $value){ //value is the attendance with subs
//            $attCalc[$i][$value->id];
            $seconds = 0;
            $last_out = 0;
            $r_project = null;
            $ws_lunch_break = 0;
            foreach ($value as $item => $items) {
                $first_in_time = 0;
                $last_out_time = 0;
                $punch_count = 0;
                if($item  == 'id') {
                    $attCalc[$i]['id']= $items;
                }
                if($item  == 'in_date') {
                    $attCalc[$i]['in_date']= $items;
                }
                if ($item == 'user_id') {
                    $attCalc[$i]['user_id'] = $items;
                    //get employee leave bool
                    $attCalc[$i]['leave'] = (new Leave)->checkEmpLeave($items,$attCalc[$i]['in_date']);
//                    $attCalc[$i]['morning'] = (new Attendance)->checkMorningPeriod($attCalc[$i]['in_date']);
//                    $attCalc[$i]['noon'] = (new Attendance)->checkNoonPeriod($attCalc[$i]['in_date']);

                }
                if ($item == 'hours_correction') {
                    $hours_correction = $items*3600;
//                    Log::info('hours_correction: '.$hours_correction);
                }
                if ($item == 'minutes_correction') {
                    $minutes_correction = $items*60;
//                    Log::info('minutes_correction: '.$minutes_correction);
                }
//                Log::info('item=> '.$item);
                if($item == 'details') {
                    $projects_arr = [];
                    $project_i = 0;
                    $project_real= [];
                    $project_real_i = 0;
                    $det_count = 0;
                    $total_seconds = 0;
                    $maxOvertimeSeconds = 0;
                    $total_seconds = 0;
                    $totalTimeSeconds = 0;
                    $overtimeSeconds = 0;
                    $extraTimeSeconds = 0;
                    $overTimeFinal = 0;
                    $extraTimeFinal = 0;

//                    Log::info($items); //this are the details only
                    foreach ($items as $rec => $recs) {
                        $t_time = null;
                        $total_time = null;
                        foreach ($recs as $det => $dets) { //go though each detail
//                            $in_time = 0;
//                            $out_time = 0;
                            if ($det == 'in_time' || 'out_time') {
                                if($det == 'in_time') {
                                    $in_time = Carbon::parse($dets);
                                    Log::info('$in_time '.$in_time);
//                                    Log::info('details_count==>'.count($items));
                                    if(count($items) >= $det_count) {
//                                        Log::info('first_in_time==> '.$in_time);
                                        $first_in_time =  (new Attendance)->toTimeZone($in_time);
                                        $attCalc[$i]['morning'] = (new Attendance)->checkMorningPeriod($first_in_time);

                                    }
                                }
                                $punch_count = count($items);
                                if($det == 'out_time') {

                                    $out_time = Carbon::parse($dets);
                                    Log::info('$out_time '.$out_time);
                                    $t_time_seconds = Project::BreakEvaluate($in_time, $out_time, $last_out);
                                    Log::info('$t_time_seconds=> '.$t_time_seconds);

                                    $seconds = $seconds+$t_time_seconds; //increment time for this employee
                                    Log::info('seconds=> '.$seconds);
                                    $last_out = $out_time;
                                    if(count($items) <= $det_count){
//                                        Log::info('last_out_time==> '.$out_time);
                                        $last_out_time = (new Attendance)->toTimeZone($out_time);
                                        $attCalc[$i]['noon'] = (new Attendance)->checkNoonPeriod($last_out_time);



                                    }

                                }
                                if ($det ==  'project_id') {
                                    $project_real[$dets] = $seconds;
//                                    Log::info(json_encode($project_real));
                                    $project_real_i++;
                                }
//                            Log::info($dets);
                            }


                            if ($det == 'project' && !empty($dets)) { //find project TODO:we have a bug here, it will record last detail project for the attendance
                                foreach ($dets as $pro => $dd) {
//                                    Log::info('>>>>>>>'.$pro.' : '.$dd);
                                    if ($pro == 'id') {
                                        $attCalc[$i]['ams_project_id'] = $dd;
                                    }
                                    if ($pro == 'pme_id') {
                                        $projects_arr[$project_i] = $dd;
//                                        Log::info("Arrray  =>".$project_i." ==>".json_encode($projects_arr));
                                        $project_i++;
                                        $attCalc[$i]['pme_id'] = implode(' - ', $projects_arr);;
//                                        $last_pme_id = $dd;
                                    }

                                }

                            } elseif (!empty($dets)) {
                                $attCalc[$i]['ams_project_id'] = "-";
                                $attCalc[$i]['pme_id'] = "-";


                            }
                            $det_count ++;


                        }
                        $r_project = array_keys($project_real, max($project_real));
//                        Log::info('Real==>project'.implode($r_project));
                        $r_project_pmeid = Project::select('pme_id')->where('id',implode($r_project))->pluck('pme_id')->first();
                        $attCalc[$i]['Project (predicted)'] = $r_project_pmeid;
                        if(is_null($attCalc[$i]['Project (predicted)'])){
                            $attCalc[$i]['Project (predicted)'] = 'N/A';
                        }
                        $lunch_break = Project::select('lunch_in')->where('id',implode($r_project))->pluck('lunch_in')->first();
//                        Log::info('Lunch break '.$lunch_break);
                        if ($lunch_break == 1) {
                            $attCalc[$i]['lunch_break'] = -1;
//                            if ($seconds > 3600) {
//                                $seconds = $seconds-3600;
//                            }else {
//                                $seconds = 0;
//                            }
                        }else {
                            $attCalc[$i]['lunch_break'] = "0";
                        }
                    }
                    $attCalc[$i]['first_in_time'] = $first_in_time;
                    $attCalc[$i]['last_out_time'] = $last_out_time;
                    $attCalc[$i]['number_of_punches'] = $punch_count;
//                    Log::info('GMDATE REAL: '.number_format((float)($seconds)/3600, 2, '.', ''));
//                    Log::info('GMDATE CORRECTED: '.number_format((float)($hours_correction+$minutes_correction)/3600, 2, '.', ''));
                    $attCalc[$i]['time_correction'] =number_format((float)($hours_correction+$minutes_correction)/3600, 2, '.', '');
                    $corrected_time = $seconds+($hours_correction+$minutes_correction);
                    Log::info('$seconds '.$seconds);
                    Log::info('$corrected_time '.$corrected_time);
                    if ($corrected_time < 0) {
                        //correct total make 0 if negative
                        $attCalc[$i]['total']= ($seconds+($hours_correction+$minutes_correction))/3600;
                    }else {
                        $attCalc[$i]['total']= gmdate("H:i:s", $seconds+($hours_correction+$minutes_correction));
                    }
//                    Log::info($attCalc[$i]);

                }

                if($item == 'profile') {
//                    Log::info('Proooofile: '.$item);
                    foreach ($items as $profile => $profiles) {
//                        Log::info($profile);
                        if ($profile == 'employee_id') {
                            $attCalc[$i]['employee_id'] = $profiles;
                        }
                    }

                }
                if($item == 'user') {
//                    Log::info('Proooofile: '.$item);
                    foreach ($items as $profile => $profiles) {
//                        Log::info($profile);
                        if ($profile == 'full_name') {
                            $attCalc[$i]['full_name'] = $profiles;
                        }
                        if ($profile == 'past_projects'){
                            foreach ($profiles as $past_project => $past_projects){
                                foreach ($past_projects as $project =>$projects){

                                    if ($project == 'pme_id'){
//                                    Log::info('assigned_pme_id=> '.json_encode($past_projects));
                                        Log::info('pme_id=> '.$projects);
                                        $attCalc[$i]['assigned_pme_id'] = $projects;
                                        if(isset($attCalc[$i]['assigned_pme_id'])){
                                            Log::info('reppp');
                                            if($attCalc[$i]['assigned_pme_id'] == $attCalc[$i]['Project (predicted)']) {
                                                $attCalc[$i]['project_error'] = 'ok';
                                            } else {
                                                $attCalc[$i]['project_error'] = 'error';
                                            }
                                        }

                                    }
                                    if ($project == 'working_shifts'){
                                        foreach ($projects as $working_shift => $working_shifts){
                                            if($working_shift == 'base') {
                                                Log::info('xbase '.$working_shifts['base']);
                                                $attCalc[$i]['ws_base'] = $working_shifts['base'];
                                                $ws_lunch_break = $working_shifts['lunch_break'];
//                                                if ($lunch_break == 1) {
//                                                    $attCalc[$i]['lunch_break'] = $working_shifts['lunch_break'];
//                                                }else {
//                                                    $attCalc[$i]['lunch_break'] = "0";
//                                                }

                                            }
                                            if($working_shift == 'details'){
                                                foreach($working_shifts as $detail => $details){
//                                                    Log::info('$details=> '.json_encode($details));
                                                    if($detail == 'name'){
                                                        $attCalc[$i]['project_workshift_name'] = $details;
//                                                        Log::info('id=> '.$details);
                                                    }
                                                    if($detail == 'details'){
//                                                        Log::info('detailss '.json_encode($details));
                                                        foreach ($details as $a){
                                                            $day = Carbon::parse($attCalc[$i]['in_date']);
                                                            $dayOfWeek = $day->format('D');
//                                                            Log::info('$dayOfWeek=> '.$dayOfWeek);
//                                                            Log::info('weekday: '.$a['weekday']);

                                                            if($a['weekday']===strtolower($dayOfWeek)){
//                                                                Log::info('id: '.$a['id']);
//                                                                Log::info('weekday: '.$a['weekday']);
//                                                                Log::info('$dayOfWeek'.strtolower($dayOfWeek));
//                                                                Log::info('start_at: '.$a['start_at']);
//                                                                Log::info('end_at: '.$a['end_at']);
//                                                                Log::info('is_weekend: '.$a['is_weekend']);
                                                                if ($a['is_weekend'] == 1) {
                                                                    $attCalc[$i]['day_type'] = 'weekend';
                                                                    $attCalc[$i]['ws_time'] = '00:00';
                                                                }else{
                                                                    $attCalc[$i]['day_type'] = 'normal';
                                                                }
                                                                $start = Carbon::parse($a['start_at']);
                                                                $end = Carbon::parse($a['end_at']);
                                                                $diff = $end->diff($start);
                                                                $totalHours = $diff->h;
                                                                $totalMinutes = $diff->i;
                                                                $totalTime = CarbonInterval::minutes($totalMinutes)->addHours($totalHours);
                                                                $totalTimeStr = $totalTime->cascade()->format('%h:%I');
                                                                $attCalc[$i]['ws_time'] = $totalTimeStr;

                                                                //calculate difference between total records working hours and required hours
                                                                if(max($attCalc[$i]['total'], 0) == 0){
                                                                    $totalWorkingHours = 0;
                                                                }else{
                                                                    $totalWorkingHours = Carbon::createFromFormat('H:i:s',max($attCalc[$i]['total'], 0));
                                                                    $ws_lunch_break_second = $ws_lunch_break *3600;
                                                                    $totalTimeSeconds = ($seconds + ($hours_correction + $minutes_correction));
                                                                    if($totalTimeSeconds > $ws_lunch_break_second){
                                                                        $totalTimeSeconds = $totalTimeSeconds -$ws_lunch_break_second;
                                                                    }else{
                                                                        $totalTimeSeconds = 0;
                                                                    }

                                                                    //added to remove fractions of seconds
//                                                                    $totalTimeSeconds = Project::eliminateSeconds($totalTimeSeconds);

                                                                    $total_base = $attCalc[$i]['ws_base'] * 3600;

                                                                    if ($a['is_weekend'] == 1) { //Calculation in Weekends
                                                                        $finalBaseWorkingHours = gmdate("H:i:s",0);
                                                                        // Define the maximum allowed overtime in seconds (ws_base)
                                                                        $maxOvertimeSeconds = $total_base;
                                                                        // Calculate the overtime in seconds
                                                                        $overtimeSeconds = max(0,  $totalTimeSeconds);
                                                                        $overtimeSeconds = min($overtimeSeconds, $maxOvertimeSeconds); // Cap at max overtime
                                                                        // Calculate extra time (working time - overtime)
                                                                        $extraTimeSeconds = max(0, $totalTimeSeconds - $overtimeSeconds);

                                                                        // Convert total seconds to hours, round it, and then convert back to seconds
                                                                        $overtimeSeconds = round($overtimeSeconds / 3600) * 3600; // Round to the nearest hour
                                                                        $extraTimeSeconds = round($extraTimeSeconds / 3600) * 3600; // Round to the nearest hour
                                                                        $totalTimeSeconds = round($totalTimeSeconds / 3600) * 3600; // Round to the nearest hour

                                                                        //Final Base Time
                                                                        $finalBaseWorkingHours = gmdate("H:i:s", 0);
                                                                        $overTimeFinal = gmdate("H:i:s", $overtimeSeconds);
                                                                        $extraTimeFinal = gmdate("H:i:s", $extraTimeSeconds);


                                                                    }else{ //Calculation in Normal Days
                                                                        // Define the maximum allowed overtime in seconds (2 hours)
                                                                        $maxOvertimeSeconds = 2 * 3600;
                                                                        // Calculate the overtime in seconds
                                                                        $overtimeSeconds = max(0, $totalTimeSeconds - $total_base);
                                                                        $overtimeSeconds = min($overtimeSeconds, $maxOvertimeSeconds); // Cap at max overtime
                                                                        // Calculate extra time (working time - overtime)
                                                                        $extraTimeSeconds = max(0, $totalTimeSeconds - $overtimeSeconds - $total_base);

                                                                        // Convert total seconds to hours, round it, and then convert back to seconds
                                                                        $overtimeSeconds = round($overtimeSeconds / 3600) * 3600; // Round to the nearest hour
                                                                        $extraTimeSeconds = round($extraTimeSeconds / 3600) * 3600; // Round to the nearest hour
                                                                        $totalTimeSeconds = round($totalTimeSeconds / 3600) * 3600; // Round to the nearest hour


                                                                        //Final Base Time
                                                                        $finalBaseWorkingHours = gmdate("H:i:s", $totalTimeSeconds - $overtimeSeconds - $extraTimeSeconds);
                                                                        $overTimeFinal = gmdate("H:i:s", $overtimeSeconds);
                                                                        $extraTimeFinal = gmdate("H:i:s", $extraTimeSeconds);

                                                                    }


//                                                                    Log::info('total working seconds ' . $totalTimeSeconds);
//                                                                    Log::info('$total_base ' . $total_base);
////                                                                    Log::info('$finalBaseWorkingHours '.$finalBaseWorkingHours/3600);
//
//                                                                    Log::info('$overTimeFinal ' . $overTimeFinal);
//                                                                    Log::info('$extraTimeFinal ' . $extraTimeFinal);

                                                                    //Calculated Time to HRMS
                                                                    $attCalc[$i]['NormalOTHours'] = $overTimeFinal;
                                                                    $attCalc[$i]['ExtraOTHours'] = $extraTimeFinal;
                                                                    $attCalc[$i]['TotalHours'] = $finalBaseWorkingHours;





//                                                                        Log::info('max total ' . max($attCalc[$i]['total'], 0));
                                                                }
                                                                $requiredHours = Carbon::createFromFormat('H:i',$totalTimeStr);
                                                                $diffHours = $requiredHours->diff($totalWorkingHours);
//                                                                    if($totalWorkingHours < $requiredHours){
//                                                                        $diffHours = $diffHours;
//                                                                    }
//                                                                    $diffStr = $diffHours->cascade()->format('%h:%I:%S');
                                                                $diffStr = sprintf('%s%02d:%02d:%02d',
                                                                    $diffHours->invert ? '-' : '',
                                                                    $diffHours->h,
                                                                    $diffHours->i,
                                                                    $diffHours->s);
                                                                $attCalc[$i]['calculated_wh_diff'] = $diffStr;

                                                            }



//                                                            Log::info('b: '.$b);
                                                        }
                                                    }
//                                                    if($detail == 'weekday'){
//                                                        Log::info('weekday=> '.$details);
//                                                        foreach($details as $det_a => $dets_a){
//                                                            if($det_a == 'weekday'){
//                                                                Log::info('weekday=> '.json_encode($dets_a));
//                                                            }
//                                                        }
//                                                    }//

                                                }
                                            }


                                        }
                                    }else{
//                                        $attCalc[$i]['project_workshift_name'] = 'N/A';
//                                        $attCalc[$i]['day_type'] = 'N/A';
//                                        $attCalc[$i]['ws_time'] = 'N/A';
//                                        $attCalc[$i]['calculated_wh_diff'] = 'N/A';
                                    }
                                }


                            }
//                            if(isset($attCalc[$i]['assigned_pme_id'])){
//                                Log::info('reppp');
//                                if($attCalc[$i]['assigned_pme_id'] == $attCalc[$i]['Project (predicted)']) {
//                                    $attCalc[$i]['project_error'] = 'ok';
//                                } else {
//                                    $attCalc[$i]['project_error'] = 'error';
//                                }
//                            }
                        }else{
                            $attCalc[$i]['assigned_pme_id'] = "N/A";
                            $attCalc[$i]['project_error'] = 'error';

                        }

                    }

                }
                //get the holiday for this day and validate if it exist or no
                if($item == 'holiday_name'){
//                    Log::info('holiday=> '.$items);
                    if(!is_null($items)){
                        $attCalc[$i]['holiday'] = true;
                        $attCalc[$i]['holiday_name'] = $items;

                    }else{
                        $attCalc[$i]['holiday'] = false;
                        $attCalc[$i]['holiday_name'] = "N/A";

                    }
//                    $attCalc[$i]['holiday_name'] = $items;
                }


            }
            if($attCalc[$i]['lunch_break'] == -1){
                try {
                    $totalTime = Carbon::createFromFormat('H:i:s', $attCalc[$i]['total']);
                    // Format the updated time back to "hh:mm:ss" format
                    $attCalc[$i]['total'] = $totalTime->format('H:i:s');

                } catch (\Exception $e) {
                    // Handle the exception gracefully, maybe log the error
                    // Optionally, you can set a default value for $attCalc[$i]['total'] or skip processing this iteration
                    $attCalc[$i]['total'] = '0'; // or any default value you want
                    // Log the error for further investigation
//                    Log::error('emp id: '.$attCalc[$i]['employee_id']);
                    Log::error('Error processing total time: ' . $e->getMessage());
                    $attCalc[$i]['project_error'] = 'error, total time issue t='.$attCalc[$i]['total'];
                }
                if(
                    isset($attCalc[$i]['NormalOTHours'])
                    && isset($attCalc[$i]['ExtraOTHours'])
                    && isset($attCalc[$i]['TotalHours'])
                    && isset($attCalc[$i]['total'])

                ){
                    $normalOTTime = Carbon::createFromFormat('H:i:s', $attCalc[$i]['NormalOTHours']);
                    $extraOTTime = Carbon::createFromFormat('H:i:s', $attCalc[$i]['ExtraOTHours']);
                    $totalTime = Carbon::createFromFormat('H:i:s', $attCalc[$i]['TotalHours']);
//                    $total = Carbon::createFromFormat('H:i:s', $attCalc[$i]['total']);

                    // Deduct 1 hour from $extraOTTime if it has sufficient time
//                    if ($extraOTTime->greaterThanOrEqualTo(Carbon::createFromFormat('H:i:s', '01:00:00'))) {
//                        $extraOTTime->subHour();
//                    } elseif ($normalOTTime->greaterThanOrEqualTo(Carbon::createFromFormat('H:i:s', '01:00:00'))) {
//                        $normalOTTime->subHour();
//                    } elseif ($totalTime->greaterThanOrEqualTo(Carbon::createFromFormat('H:i:s', '01:00:00'))) {
//                        $totalTime->subHour();
//                    } else {
//                        // Set all to 0
//                        $normalOTTime = Carbon::createFromFormat('H:i:s', '00:00:00');
//                        $extraOTTime = Carbon::createFromFormat('H:i:s', '00:00:00');
//                        $totalTime = Carbon::createFromFormat('H:i:s', '00:00:00');
//                    }
                    $attCalc[$i]['NormalOTHours'] = $normalOTTime->format('H:i:s');
                    $attCalc[$i]['ExtraOTHours'] = $extraOTTime->format('H:i:s');
                    $attCalc[$i]['TotalHours'] = $totalTime->format('H:i:s');
//                    $attCalc[$i]['total'] = $normalOTTime->add($extraOTTime)->add($totalTime);
                    $total = Carbon::createFromFormat('H:i:s', '00:00:00');

                    $total->addHours($normalOTTime->hour);
                    $total->addMinutes($normalOTTime->minute);
                    $total->addSeconds($normalOTTime->second);

                    $total->addHours($extraOTTime->hour);
                    $total->addMinutes($extraOTTime->minute);
                    $total->addSeconds($extraOTTime->second);

                    $total->addHours($totalTime->hour);
                    $total->addMinutes($totalTime->minute);
                    $total->addSeconds($totalTime->second);

                    // Convert total seconds back to time format

                    // Assign total hours to the new variable
                    $attCalc[$i]['total'] = $total->format('H:i:s');

                    Log::info('$attCalc[$i][NormalOTHours'.$attCalc[$i]['NormalOTHours']);
                    Log::info('$attCalc[$i][ExtraOTHours'.$attCalc[$i]['ExtraOTHours']);
                    Log::info('$attCalc[$i][NormalOTHours'.$attCalc[$i]['TotalHours']);
                    Log::info('$attCalc[$i][total'.$attCalc[$i]['total']);



                }

            }
            $attCalc[$i]['lunch_break'] = $ws_lunch_break;
            // Fetch the existing total time
            Log::info('calll'.$attCalc[$i]['total']);
            if (!empty($attCalc[$i]['total']) && $attCalc[$i]['total'] != '0') {
                $totalTime = Carbon::createFromFormat('H:i:s', $attCalc[$i]['total']);
            } else {
                // Handle the zero or invalid case, e.g., set $totalTime to a default value
                $totalTime = Carbon::createFromFormat('H:i:s', '00:00:00');
            }

//            $totalTime = Carbon::createFromFormat('H:i:s', $attCalc[$i]['total']);
            //total without rounding
            $totalTimeNoRound = $attCalc[$i]['total'];
            $attCalc[$i]['totalTimeNoRound'] = $totalTimeNoRound;
            // Convert total time to seconds
            $totalInSeconds = ($totalTime->hour * 3600) + ($totalTime->minute * 60) + $totalTime->second;

            // Round to the nearest hour in seconds
            $totalRoundedSeconds = round($totalInSeconds / 3600) * 3600;

            // Convert back to H:i:s format and reassign
            $attCalc[$i]['total'] = gmdate('H:i:s', $totalRoundedSeconds);



            $i++;
        }

        return collect($attCalc);

    }
    public static function BreakEvaluate ($in, $out, $lout) { //this function will evaluate time jumps
        $in = Carbon::parse($in);
        $out = Carbon::parse($out);
        $lout = Carbon::parse($lout);
        $diff = $lout->diffInSeconds($in);
        $normal_diff = $in->diffInSeconds($out);
        Log::info('DIFF: '.$diff);
        Log::info('$normal_diff: '.$normal_diff);

        if ($lout = 0) {
            Log::info('$normal_diff chosen');

            return $normal_diff;
        } else {
            if ($diff > 7200) { // if left for a break more than two hours return current time period only
                Log::info('$normal_diff chosen');

                return $normal_diff;
            } else {
                Log::info('$normal_diff+$diff chosen');

                return $normal_diff+$diff; // if left for a break less than two hours return current time period with the misplaced break
            }
        }

    }
    public function ws(): BelongsToMany
    {
        return $this->belongsToMany(ProjectWorkingShift::class)
            ->withPivot(['working_shift_id']);
    }


    function deleteGeofence($geofenceId): array
    {
        try {
            $url = env('vts_api_path') . '/destroy_geofence';
            $userApiHash = env('vts_api_token');
            $disableVerify = (bool)env('HTTP_VERIFY_SSL', true); // Default to true (enabled)

            $response = Http::withOptions(['verify' => $disableVerify])
                ->get($url, [
                    'lang' => 'en',
                    'user_api_hash' => $userApiHash,
                    'geofence_id' => $geofenceId,
                ]);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'message' => 'Geofence deleted successfully',
                    'data' => $response->json()
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete geofence',
                    'data' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }



}
