<?php

namespace App\Models\Tenant\Attendance;


use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Attendance\Relationship\RecurringAttendanceRelationship;
use App\Models\Tenant\Employee\Rules\RecurrentAttendanceRules;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class RecurringAttendance extends TenantModel
{
    use StatusRelationship, RecurringAttendanceRelationship , RecurrentAttendanceRules;
    use SoftDeletes;

    protected $fillable = [
        'status_id','working_shift_id','in_time','out_time','added_by','project_id'
    ];

    // Define an accessor to get the time in AM/PM format
    public function getYourTimeColumnAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value)->format('g:i A');
    }

    // Define a mutator to store the time in 24-hour format
    public function setYourTimeColumnAttribute($value)
    {
        $this->attributes['your_time_column'] = Carbon::createFromFormat('g:i A', $value)->format('H:i:s');
    }
}
