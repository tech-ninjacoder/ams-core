<?php

namespace App\Models\Tenant\Employee\Rules;


trait HelmetRules
{
    public function createdRules()
    {
        return [
            'imei' => 'required|min:2',
            'tenant_id' => 'required',
            'department_id' => 'nullable|exists:departments,id'
        ];
    }

    public function updatedRules()
    {
        return $this->createdRules();
    }
}
