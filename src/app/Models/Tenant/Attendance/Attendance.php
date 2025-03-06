<?php

namespace App\Models\Tenant\Attendance;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Relationship\AttendanceRelationship;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Utility\HrmsBatch;
use App\Models\Tenant\Utility\HrmsSync;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\Concerns\Has;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use SimpleXMLElement;

class Attendance extends TenantModel
{
    use AttendanceRelationship;

    protected $fillable = [
        'in_date', 'user_id', 'status_id', 'tenant_id', 'working_shift_id', 'behavior', 'hours_correction','minutes_correction','seconds_correction', 'synch_date','source'
    ];

    public static array $statuses = [
        'approve', 'reject', 'cancel'
    ];

    public function scopeDate(Builder $query, $date)
    {
        return $query->whereDate('in_date', $date);
    }
    public function users(): HasOne
    {
        return $this->hasOne(User::class, 'id');
    }
    public function toTimeZone($time){
        $timeZone = Config::get('app.timezone');

        // Specify the original time and its format
        $originalTime = $time;
        $format = 'H:i:s';

        // Create a Carbon instance for the original time
        $carbonOriginalTime = Carbon::parse($originalTime);

        // Convert the time to the configured time zone
        $convertedTime = $carbonOriginalTime->setTimezone($timeZone);

        // Output the converted time
        return $convertedTime->toDateTimeString();

    }

    public function totalhours() {
        $hours = null;
        $attendances = AttendanceDetails::count();
        return $attendances;
    }
    public function checkMorningPeriod($in_date){
        $startDateTime = Carbon::parse($in_date);
        if ($startDateTime->hour < 12) {
            return true;
            // The user was in the morning.
        } else {
            return 'false';
            // The user was in the afternoon.
        }
    }
    public function checkNoonPeriod($in_date){
        $startDateTime = Carbon::parse($in_date);
        if ($startDateTime->hour > 12) {
            return true;
            // The user was in the afternoon.
        } else {
            // The user was in the morning.
            return 'false';
        }
    }

    public function upload_hrms($resultWithAdditionalFields, $attendance_date){
        $shortname = env('hrms_shortname');
        $password = env('hrms_pass');
        $username = env('hrms_user');

        $batch = new HrmsBatch();
        $batch->status = false;
        $batch->batch_date = Carbon::parse($attendance_date)->toDateString();
        $batch->save();



//        $projects = Project::all();
        $attendances = $resultWithAdditionalFields;

//        $projects = Project::whereIn('id',$ids)->get();

        $attendance_xml = [];

        foreach ($attendances as $attendance) {
            $attendance_xml[] = [
                'employee_id' => htmlspecialchars($attendance['employee_id']),
                'user_id' => $attendance['user_id'],
                'Project (predicted)' => htmlspecialchars($attendance['Project (predicted)']),
                'in_date' => $attendance['in_date'],
                'morning' => $attendance['morning'],
                'noon' => $attendance['noon'],
                'assigned_pme_id' => $attendance['assigned_pme_id'],
                'total' => $this->time_to_int($attendance['total']),
                'calculated_wh_diff' => $this->time_to_int($attendance['calculated_wh_diff']),
                'day_type' => $attendance['day_type'],
                'holiday' => $attendance['holiday'],
                'id' => $attendance['id'],
                'TotalHours' => $this->time_to_int($attendance['TotalHours']),
                'NormalOTHours' => $this->time_to_int($attendance['NormalOTHours']),
                'ExtraOTHours' => $this->time_to_int($attendance['ExtraOTHours']),
                'ws_base' => $attendance['ws_base'],


            ];
        }

        $xml = '<?xml version="1.0"?><Attandance>';
        foreach ($attendance_xml as $attendance_) {
            $xml .= '<Employee>';
            $hrms_sync = new HrmsSync();

            $xml .= '<EmpID>' . htmlspecialchars($attendance_['employee_id']) . '</EmpID>';
            $hrms_sync->emp_id = htmlspecialchars($attendance_['employee_id']);
            $hrms_sync->status = false;
            $hrms_sync->attendance_id = $attendance_['id'];
            $hrms_sync->batch_id = $batch->id;


            $xml .= '<ProjectID>' . htmlspecialchars($attendance_['assigned_pme_id']) . '</ProjectID>';
            $hrms_sync->ProjectID = htmlspecialchars($attendance_['assigned_pme_id']);
            $xml .= '<DutyDate>' . htmlspecialchars($attendance_['in_date']) . '</DutyDate>';
            $hrms_sync->attendance_date = Carbon::parse($attendance_['in_date'])->toDateString();

            $TotalHours = $attendance_['TotalHours'];
            $OffDayOTHours = 0;
            $HolidayOTHours = 0;
            $NormalOTHours = $attendance_['NormalOTHours'];
            $ExtraOTHours = $attendance_['ExtraOTHours'];
            $OffDayOrHoliday = 0;
            $BaseWorkShiftHours = $attendance_['ws_base'];



            if($attendance_['holiday'] === true){
                $TotalHours = 0;
                $OffDayOTHours = 0;
                $HolidayOTHours = $attendance_['TotalHours'] + $attendance_['NormalOTHours'] + $attendance_['ExtraOTHours'];
                $NormalOTHours = 0;
                $ExtraOTHours = 0;
                $OffDayOrHoliday = 'holiday';
            }elseif($attendance_['day_type'] == 'weekend'){
                $TotalHours = 0;
                $OffDayOTHours = $attendance_['TotalHours'] + $attendance_['NormalOTHours'] + $attendance_['ExtraOTHours'];
                $HolidayOTHours = 0;
                $NormalOTHours = 0;
                $ExtraOTHours = 0;
                $OffDayOrHoliday = 'weekend';

            }else{
                $TotalHours = $attendance_['TotalHours'];
                $OffDayOTHours = 0;
                $HolidayOTHours = 0;
                $NormalOTHours = $attendance_['NormalOTHours'];
                $ExtraOTHours = $attendance_['ExtraOTHours'];
                $OffDayOrHoliday = 'normal';
            }

            //Total Hours Normal Day and Weekend
            $xml .= '<TotalHours>'.$TotalHours.'</TotalHours>';
            $hrms_sync->TotalHours = $TotalHours;

            //Vacation Day Overtime
            $xml .= '<OffDayOTHours>'.$OffDayOTHours.'</OffDayOTHours>';
            $hrms_sync->OffDayOTHours = $OffDayOTHours;

            //Holiday Overtime
            $xml .= '<HolidayOTHours>' .$HolidayOTHours. '</HolidayOTHours>';
            $hrms_sync->HolidayOTHours = $HolidayOTHours;

            //Off day or Holiday
            $xml .= '<OffDayOrHoliday>'.$OffDayOrHoliday.'</OffDayOrHoliday>';
            $hrms_sync->OffDayOrHoliday = $OffDayOrHoliday;

            //Normal Overtime
            $xml .= '<NormalOTHours>'.$NormalOTHours.'</NormalOTHours>';
            $hrms_sync->NormalOTHours = $NormalOTHours;

            //Extra Overtime
            $xml .= '<ExtraOTHours>'.$ExtraOTHours.'</ExtraOTHours>';
            $hrms_sync->ExtraOTHours = $ExtraOTHours;

            //Base Workshift Time
            $xml .= '<BaseWorkShiftHours>'.$BaseWorkShiftHours.'</BaseWorkShiftHours>';
            $hrms_sync->BaseWorkShiftHours = $BaseWorkShiftHours;

            $xml .= '<Remarks>AMS '. htmlspecialchars($send_time = Carbon::now()).'</Remarks>';
            $hrms_sync->note = ' ';

            $xml .= '<BatchNo>' . htmlspecialchars($batch->id) . '</BatchNo>';
            $hrms_sync->batch_id = $batch->id;

            $xml .= '<ExportUser>' .htmlspecialchars($attendance_['user_id']) .'</ExportUser>';
            $hrms_sync->ExportUser = $attendance_['user_id'];
            $hrms_sync->save();
            $xml .= '</Employee>';
        }

        $xml .= '</Attandance>';

        Log::info('$xml=> '.$xml);

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/xml',
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password)
        ];
        $body = $xml;
        $request = new Request('POST', 'http://protectauh.fortiddns.com:8082/Api/Attendance/UploadEmployeeAttendance', $headers, $body);
        $response = $client->sendAsync($request)->wait();
        echo $response->getBody();
//        Log::info('$request=> '.json_encode($request));
        if ($response->getStatusCode() == 200) {
            echo 'attendance uploaded successfully.';
            $batch->status = true;
            $batch->save();

//            $response->each(function ($message) {
//                echo "Status ".$message->status;
//            });
//            Log::info('resss ===> '.$response->getBody());
//            $data = $response->getBody();
//            // Loop through each message
//            foreach ($data as $message) {
//                $empID = $message['EmpID'];
//                $dutyDate = $message['DutyDate'];
//                $status = $message['Status'];
////                Log::info('message=> '.json_encode($message));
//
//                // Perform operations with the parsed message
//                // Example: Display the message details
//                echo "EmpID: $empID\n";
//                echo "DutyDate: $dutyDate\n";
//                echo "Status: $status\n";
//                echo "\n";
//            }
            $responseXml = new SimpleXMLElement($response->getBody());

            $tableElements = $responseXml->xpath('//Table');
            foreach ($tableElements as $table) {
                $empID = (string)$table->EmpID;
                $dutyDate = (string)$table->DutyDate;
                $status = (string)$table->Status;

                // Output the extracted values
                echo "EmpID: $empID\n";
                echo "DutyDate: $dutyDate\n";
                echo "Status: $status\n";
                echo "---\n";


                $hrms_sync_update = HrmsSync::where('batch_id',$batch->id)->where('emp_id',$empID)->first();
                if($hrms_sync_update) {


                    if ($status == 'Done') {
                        $hrms_sync_update->status = true;
                    } else {
                        $hrms_sync_update->status = false;
                        $hrms_sync_update->Remarks = $status;
                    }
                    $hrms_sync_update->save();

                    //update the synch date in the attendance record
                    $attendance_record = Attendance::find($hrms_sync_update->attendance_id);
                    if ($attendance_record) {
                        $attendance_record->synch_date = now(); // Set synch_date to current timestamp
                        $attendance_record->save(); // Save the updated record
                    }
                }


            }
//            Log::info($response = json_encode($response));
            return $response->getBody();

        } else {
            echo 'An error occurred while uploading project details.';
            $batch->status = false;
            $batch->save();

            return $response->getBody();
        }
//        Log::info('success '.json_encode($response->getBody()->getContents()));
    }
    function time_to_int($time) {
        $timeParts = explode(':', $time);

        if (count($timeParts) !== 3) {
            // Invalid time format, handle the error appropriately
            return 0; // or throw an exception, return an error message, etc.
        }

        $hours = intval($timeParts[0]);
        $minutes = intval($timeParts[1]);
        $seconds = intval($timeParts[2]);

        $totalHours = ($hours * 60 + $minutes + $seconds / 60) / 60;

        return $totalHours;

     }

    public function AbsenseCalc($date)
    {
        // Get the list of user_ids that have attendance for the given date
        $rawAttendance = Attendance::where('in_date', $date)
            ->pluck('user_id');

        // Get the list of users who are considered employees and have active status
        $notEmp = User::where('is_in_employee', 1)
            ->whereNull('deleted_at')
            ->where('status_id', 1)
            ->pluck('id');

        // Get the users assigned to projects and check if they were active on the given date
        $rawAssigned = ProjectUser::whereIn('user_id', $notEmp)
            ->where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('end_date', '>=', $date)
                    ->orWhereNull('end_date');
            })
            ->distinct()
            ->pluck('user_id');

        // Get the users who were assigned to projects but did not mark attendance
        $rawAbsence = User::whereIn('id', $rawAssigned)
            ->whereNotIn('id', $rawAttendance)
            ->where('is_in_employee', 1)
            ->count();  // Directly count the users who have an absence

        return $rawAbsence;
    }

    public function Assigned($date)
    {

        // Get the list of users who are considered employees and have active status
        $notEmp = User::where('is_in_employee', 1)
            ->whereNull('deleted_at')
            ->where('status_id', 1)
            ->pluck('id');

        // Get the users assigned to projects and check if they were active on the given date
        return ProjectUser::whereIn('user_id', $notEmp)
            ->where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('end_date', '>=', $date)
                    ->orWhereNull('end_date');
            })
            ->distinct()
            ->count();
    }



}
