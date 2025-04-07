<?php

namespace App\Console\Commands;

use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Holiday\Holiday;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Utility\HrmsBatch;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class hrms_attendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hrms:attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
//        $date = Carbon::today()->toDateString();

        // Get the last batch from the table
        $lastBatch = HrmsBatch::orderBy('created_at', 'desc')->first();

        // Check if a batch exists
//        if ($lastBatch) {
//            Log::info('$lastBatch '.$lastBatch->created_at);
//
//            // Get the start date of the batch
//            $startDate = Carbon::parse($lastBatch->batch_date);
////            $startDate = Carbon::parse('02-04-2023');
//
//
//            // Get today's date
//            $today = Carbon::today()->endOfDay();
////            $today = Carbon::parse('29-05-2023')->endOfDay();
//
//
//            // Loop through the days from the start date until today
//            while ($startDate->lte($today)) {
//                Log::info('today');
//                // Process each day
//                $this->processDay($startDate);
//                echo "Date: $startDate\n";
//
//
//                // Move to the next day
//                $startDate->addDay();
//            }
//        }else{
//            $first_attendance = Attendance::first();
//
//            // Get the start date of the batch
//            $startDate = Carbon::parse($first_attendance->in_date);
//
//            // Get today's date
//            $today = Carbon::today()->endOfDay();
//
//            // Loop through the days from the start date until today
//            while ($startDate->lte($today)) {
//                Log::info('today');
//                // Process each day
//                $this->processDay($startDate);
//                echo "Date: $startDate\n";
//
//
//                // Move to the next day
//                $startDate->addDay();
//            }
//        }

//        $updated_attendances = Attendance::whereNull('synch_date')
//            ->orWhereDate('updated_at', '>', DB::raw('synch_date'))
//            ->pluck('in_date');

        $updated_attendances = Attendance::where(function ($query) {
            $query->whereNull('synch_date')
                ->orWhereRaw('DATE(updated_at) > DATE_ADD(DATE(synch_date), INTERVAL 1 DAY)');
        })->pluck('in_date');





        if($updated_attendances){
            $uniqueDates = array_unique($updated_attendances->toArray());
        }
        foreach ($uniqueDates as $key => $date){
            $past_date = Carbon::parse($date)->toDateString();
            $this->processDay($past_date);
            echo "Date: $past_date\n";

            Log:info('date=> '.Carbon::parse($date)->toDateString());
        }
        Log::info('updated_attendances=> '.json_encode($updated_attendances));

        return 0;
    }

    public function processDay($attendance_date)
    {
        // Process the day with the given date
        // Implement your logic here for processing each day
        $date = $attendance_date;


        $rawAttendance =  Attendance::where('in_date',$date)
            ->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('synch_date')
                        ->where('status_id', 7);
                })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('status_id', 7)
                            ->whereRaw('DATE(updated_at) > DATE_ADD(DATE(synch_date), INTERVAL 1 DAY)');
                    });
            })            ->with([
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
            },
            'user.past_projects.WorkingShifts',
            'user.past_projects.WorkingShifts.details',
        ])->get();


//        Log::info('$rawAttendance=> '.json_encode($rawAttendance));


        $CalculatedAttendance = Project::calculateAttendanceDaily($rawAttendance);
        $CalculatedAttendance = json_decode(json_encode($CalculatedAttendance), true);

        $additionalFields = [
            'assigned_pme_id' => null,
            'project_error' => null,
            'project_workshift_name' => null,
            'day_type' => null,
            'ws_time' => null,
            'ws_base' => null,
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

// Displaying the updated result
//        echo json_encode($resultWithAdditionalFields);

        Log::info('$CalculatedAttendance=> '.json_encode($resultWithAdditionalFields));
        $hrms_respond = (new Attendance)->upload_hrms($resultWithAdditionalFields,$date);
        Log::info('$hrms_respond '.$hrms_respond);
        return $hrms_respond;


//        Log::info('attendance_daily=> '.json_encode($CalculatedAttendance));




    }
}
