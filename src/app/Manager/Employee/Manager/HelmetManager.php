<?php


namespace App\Manager\Employee\Manager;


use App\Services\Tenant\Employee\DesignationService;
use App\Services\Tenant\Employee\HelmetService;
use App\Services\Tenant\Employee\ProviderService;

class HelmetManager extends BaseManager implements EmployeeManagerContract
{

    public function assignEmployees($employees)
    {
        $employees = is_array($employees) ? $employees : func_get_args();

        return resolve(HelmetService::class)
            ->setHelmetId($this->getModel())
            ->endPreviousHelmetOfUsers($employees)
            ->assignToUsers($employees);
    }
}
