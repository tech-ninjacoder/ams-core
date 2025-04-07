<?php

namespace App\Console\Commands;

use App\Models\Core\Auth\Profile;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Leave\LeaveType;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:leaves';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Leaves from HRMS';

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
        $leaves = [];
        $count = 0;
        Log::info('<--- COMMAND import:leaves STARTED  --->');

        $response = Http::withBasicAuth('olive', 'PmE@7oLv!')->get(env('hrms_api_path').'/LeaveDetails/Get?fromDate='.Carbon::today()->subYear()->toDateString().'&toDate='.Carbon::now()->addMonths(1)->toDateString().'&shortName=PME');

//        $response = Http::withBasicAuth('olive', 'PmE@7oLv!')->get(env('hrms_api_path').'LeaveDetails/Get?fromDate='.Carbon::today()->toDateString().'&toDate='.Carbon::tomorrow()->toDateString().'&shortName=PME');
        Log::info(env('hrms_api_path').'/LeaveDetails/Get?fromDate='.Carbon::today()->toDateString().'&toDate='.Carbon::now()->addMonths(1)->toDateString().'&shortName=PME');
        $response = json_decode($response, true);
//        Log::info($response);

        if (!empty($response)) {
            foreach ($response as $key => $value) {
                if (Leave::find($value['LeaveID'])) {
                    Log::info($value['LeaveID']);
                    Log::info('Leave ID '.$value['LeaveID'].' exists in DB ==> Skip');
                }else {
                    $profile = Profile::where('employee_id',$value['EmpID'])->first();
                    $leave_type = LeaveType::where('name', $value['LeaveType'])->first();
                    $timestamp = Carbon::createFromFormat('Y/m/d', '2016/05/05')->timestamp;
                    $start_at= Carbon::createFromFormat('d/m/Y', $value['LeaveFromDate']);
                    $end_at = Carbon::createFromFormat('d/m/Y', $value['LeaveToDate']);
                    $diff = $start_at->diffInDays($end_at);
                    $diff_text = null;
                    if ($diff > 1) {
                        $diff_text = 'multi_day';
                    } else {
                        $diff_text = 'single_day';
                    }
                    if (empty($leave_type)) {
                        $leave_t = new LeaveType();
                        $leave_t->name = $value['LeaveType'];
                        $leave_t->alias = str_replace('-', ' ', $value['LeaveType']);
                        $leave_t->type = 'updaid';
                        $leave_t->amount = null;
                        $leave_t->special_percentage = 5.50;
                        $leave_t->is_enabled = 1;
                        $leave_t->is_earning_enabled = 0;
                        $leave_t->tenant_id = 1;
                    }
                    Log::info($value['LeaveType']);

                    if (!empty($profile)) {
                        $employee = Profile::where('employee_id',$value['EmpID'])->pluck('user_id');
                        $Leave = new Leave();
                        $Leave->id = $value['LeaveID'];
                        $Leave->user_id = $profile->user_id;
                        $Leave->leave_type_id = $leave_type->id ;
                        $Leave->status_id = 12;
                        $Leave->working_shift_details_id = null;
                        $Leave->date = Carbon::today();
                        $Leave->start_at = Carbon::createFromFormat('d/m/Y', $value['LeaveFromDate']);
                        $Leave->end_at = Carbon::createFromFormat('d/m/Y', $value['LeaveToDate']);
                        $Leave->duration_type =$diff_text;
                        $Leave->assigned_by = 2;
                        $Leave->tenant_id = 1;
                        $Leave->save();
                        Log::info('Leave Created');
                    }else {
                        Log::info('emp not found');
                    }
                }
            }

        }else return null;
//        Log::info("DEVICES ARRAY===> ".json_encode($imei)."END OF DEVICES ARRAY");
        Log::info('<--- COMMAND import:leaves ENDED --->');
        return 0;

    }
    public function GetLeaves()
    {
//        $leaves = [];
//        $count = 0;
//
//
//        $response = Http::get(env('hrms_api_path').'/LeaveDetails/Get?fromDate='.'&toDate='.'&shortName=PME');
//        $response = $response->json();
//
//        //check response and return each device id with it's leave
//        if (!empty($response)) {
//            foreach ($response as $key => $value) {
////                Log::info($key);
//                if ($key == 'items') {
//
//
//            }
//
//        }else return null;
////        Log::info("DEVICES ARRAY===> ".json_encode($leaves)."END OF DEVICES ARRAY");
//        return $leaves;
    }
}
