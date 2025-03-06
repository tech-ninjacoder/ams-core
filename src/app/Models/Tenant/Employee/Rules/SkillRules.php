<?php

namespace App\Models\Tenant\Employee\Rules;


trait SkillRules
{
    public function createdRules()
    {
        return [
            'name' => 'required|min:1',
//            'tenant_id' => 'required',
//            'department_id' => 'nullable|exists:departments,id'
        ];
    }

    public function updatedRules()
    {
        return $this->createdRules();
    }
}
