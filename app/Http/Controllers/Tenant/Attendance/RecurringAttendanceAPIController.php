<?php


namespace App\Http\Controllers\Tenant\Attendance;


use App\Http\Controllers\Controller;
use App\Models\Core\Status;
use App\Models\Tenant\Attendance\RecurringAttendance;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\Skill;

class RecurringAttendanceAPIController extends Controller
{
    public function index()
    {
        return RecurringAttendance::select('id')->get();
    }

    public function statuses () {
        return Status::where('type','recurring_attendance')->get();
    }
}
