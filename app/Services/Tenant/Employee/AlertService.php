<?php

namespace App\Services\Tenant\Employee;

use App\Models\Tenant\Employee\EmployeeAlerts;
use App\Services\Tenant\TenantService;

class AlertService extends TenantService
{
    protected $alertId;

    public function __construct(EmployeeAlerts $Alerts )
    {
        $this->model = $Alerts;
    }


}
