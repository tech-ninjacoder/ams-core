<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Filters\Tenant\DepartmentFilter;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\WorkersProviders;

class WorkersProviderAPIController extends Controller
{
     public function index()
    {
        return WorkersProviders::select('id','name','contract_type')->get();
    }
}
