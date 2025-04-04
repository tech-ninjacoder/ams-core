<?php


namespace App\Manager\Employee\Manager;


use App\Services\Tenant\Employee\DesignationService;
use App\Services\Tenant\Employee\ProviderService;

class ProviderManager extends BaseManager implements EmployeeManagerContract
{

    public function assignEmployees($employees)
    {
        $employees = is_array($employees) ? $employees : func_get_args();

        return resolve(ProviderService::class)
            ->setProviderId($this->getModel())
            ->endPreviousProviderOfUsers($employees)
            ->assignToUsers($employees);
    }
}