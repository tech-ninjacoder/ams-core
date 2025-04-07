<?php


namespace App\Manager\Employee\Manager;


use App\Services\Tenant\Employee\GatePassService;

class GatePassManager extends BaseManager implements EmployeeManagerContract
{

    public function assignEmployees($employees)
    {
        $employees = is_array($employees) ? $employees : func_get_args();

        return resolve(GatePassService::class)
            ->setGatePassId($this->getModel())
            ->endPreviousGatePassOfUsers($employees)
            ->assignToUsers($employees);
    }
}
