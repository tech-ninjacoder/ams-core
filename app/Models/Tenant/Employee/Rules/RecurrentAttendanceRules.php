<?php

namespace App\Models\Tenant\Employee\Rules;


trait RecurrentAttendanceRules
{
    public function createdRules()
    {
        return [
            'status_id' => 'required',
            'working_shift_id' => 'required|exists:working_shifts,id',
            'in_time' => 'required',
            'out_time' => 'required',
//            'added_by' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',

//            'tenant_id' => 'required',
//            'department_id' => 'nullable|exists:departments,id'
        ];
    }

    public function updatedRules()
    {
        return $this->createdRules();
    }
}
