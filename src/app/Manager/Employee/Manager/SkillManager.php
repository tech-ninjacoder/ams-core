<?php


namespace App\Manager\Employee\Manager;


use App\Services\Tenant\Employee\DesignationService;
use App\Services\Tenant\Employee\HelmetService;
use App\Services\Tenant\Employee\ProviderService;
use App\Services\Tenant\Employee\SkillService;

class SkillManager extends BaseManager implements EmployeeManagerContract
{

    public function assignEmployees($employees)
    {
        $employees = is_array($employees) ? $employees : func_get_args();

        return resolve(SkillService::class)
            ->setSkillId($this->getModel())
            ->endPreviousSkillOfUsers($employees)
            ->assignToUsers($employees);
    }
}
