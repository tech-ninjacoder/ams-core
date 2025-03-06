<?php

namespace App\Http\Requests\Tenant\Attendance;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Attendance\RecurringAttendance;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\Skill;

class RecurringAttendanceRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules( new RecurringAttendance());
    }
}
