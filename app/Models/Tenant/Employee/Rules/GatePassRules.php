<?php

namespace App\Models\Tenant\Employee\Rules;


trait GatePassRules
{
    public function createdRules()
    {
        return [
            'name' => 'required|min:2',
            'description' => 'required',
            'valid' => 'required',
            'gate_passe_type_id' => 'nullable|exists:gate_passe_types,id'
        ];
    }

    public function updatedRules()
    {
        return $this->createdRules();
    }
}
