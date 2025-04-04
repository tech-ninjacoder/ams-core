<?php

namespace App\Models\Tenant\Request\Rules;


trait TransferRequestRules
{
    public function createdRules()
    {
        return [
            'title' => 'required|min:2',
            'description' => 'required|min:10',
            'department_id' => 'nullable|exists:departments,id'
        ];
    }

    public function updatedRules()
    {
        return $this->createdRules();
    }
}
