<?php

namespace App\Models\Tenant\Leave;

use App\Models\Core\Status;
use App\Models\Tenant\Leave\Relationship\LeaveRelationship;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Leave extends TenantModel
{
    use HasFactory, LeaveRelationship;

    public static array $statuses = [
        'canceled', 'approved', 'rejected', 'pending', 'bypassed'
    ];

    public static array $duration_types = [
        'multi_day', 'single_day', 'last_half', 'hours', 'first_half'
    ];

    public static array $day_duration_types = [
        'multi_day', 'single_day',
    ];

    public static array $half_day_duration_types = [
        'last_half', 'first_half'
    ];

    public static array $single_day_duration_types = [
        'single_day'
    ];

    public static array $multi_day_duration_types = [
        'multi_day'
    ];

    public static array $hours_duration_types = [
        'hours'
    ];

    protected $fillable = [
        'user_id', 'assigned_by', 'date', 'leave_type_id', 'status_id', 'working_shift_details_id', 'start_at', 'end_at', 'duration_type', 'tenant_id',
    ];

    public function setStatus($status, $user_id = null): Leave
    {
        if ($status instanceof Status) {
            $status = $status->id;
        } elseif (is_string($status)) {
            $status = Status::findByNameAndType('status_' . $status, 'leave');
            $status = $status->id;
        }

        LeaveStatus::updateOrCreate([
            'leave_id' => $this->id,
            'status_id' => $status
        ], [
            'leave_id' => $this->id,
            'status_id' => $status,
            'reviewed_by' => $user_id ?: auth()->id()
        ]);

        return $this;
    }

    public function getHourPercentage(WorkingShiftDetails $details)
    {
        return (Carbon::parse($this->start_at)->diffInSeconds(Carbon::parse($this->end_at)) / $details->getWorkingHourInSeconds());
    }

    public function checkEmpLeave($emp_id,$date) {
        $leave = Leave::where('user_id',$emp_id)
            ->where('start_at', '<=', $date)
            ->where('end_at', '>=', $date)
            ->first();
//        Log::info('Leave => '.$leave);
        if(!is_null($leave)){
            return true;
        }else{
            return false;
        }
    }

}
