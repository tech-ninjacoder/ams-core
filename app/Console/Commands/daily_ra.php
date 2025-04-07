<?php

namespace App\Console\Commands;

use App\Http\Controllers\Tenant\Employee\ManualAttendanceController;
use App\Models\Core\Status;
use App\Models\Tenant\Attendance\RecurringAttendance;
use App\Services\Tenant\Attendance\AttendanceService;
use Illuminate\Console\Command;
use App\Models\Core\Auth\User; // Ensure this is the correct namespace for your User model
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class daily_ra extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ra:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Daily Recurring Attendance ';
    protected $attendanceService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AttendanceService $attendanceService)
    {
        parent::__construct();
        $this->attendanceService = $attendanceService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $statusActive = Status::where('name', 'status_active')->where('type', 'recurring_attendance')->pluck('id');

        $auth_user = User::find(2);

        if (!$auth_user) {
            $this->error('User not found.');
            return;
        }

        auth()->login($auth_user);

        Log::info('$statusActive ' . $statusActive);

        $rad = RecurringAttendance::whereIn('status_id', $statusActive)->with('users')->get();
        // Loop through each RecurringAttendance record
        foreach ($rad as $ra) {
            // Check if 'users' field exists and is not empty
            if (isset($ra->users) && !empty($ra->users)) {
                // Loop through each user
                foreach ($ra->users as $user) {
                    try {
                        $date_in = Carbon::today(); // Your date field
                        $time_in = $ra->in_time;   // Your time field
                        $date_in = $date_in->setTimeFromTimeString($time_in);

                        $date_out = Carbon::today();  // Your date field
                        $time_out = $ra->out_time;   // Your time field
                        $date_out->setTimeFromTimeString($time_out);

                        $requestData = [
                            'employee_id' => $user->id,
                            'in_date' => Carbon::today()->toDateString(),
                            'note' => 'RA: '.$ra->id,
                            'in_time' => $date_in->toDateTimeString(),
                            'out_time' => $date_out->toDateTimeString(),
                            'project_id' => $ra->project_id,
                            'review_by' => 1,
                            'status_name'=>'pending'
                        ];
                        $request = new Request($requestData);
                        $controller = new ManualAttendanceController($this->attendanceService);
                        $response = $controller->store($request);

                        $this->info($response->getContent());
                        Log::info('modellll '.json_encode($this->attendanceService));
                    } catch (\Exception $e) {
                        // Log the error and continue with the next user
                        Log::error("Error processing attendance for user {$user->id} in recurring attendance {$ra->id}: " . $e->getMessage());
                        $this->error("Error processing attendance for user {$user->id} in recurring attendance {$ra->id}: " . $e->getMessage());
                    }
                }
            }
        }
        return 0;
    }
}
