<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\EmployeeAlertFilter;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\EmployeeAlerts;
use App\Services\Tenant\Employee\AlertService;
use Illuminate\Http\Request;

class EmployeeAlertController extends Controller
{
    public function __construct(AlertService $service, EmployeeAlertFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }
//    //
    public function index(){
//        $alerts = Alerts::all();
        return $this->service
            ->filters($this->filter)

            //            ->filters($this->filter)
            ->with('users')
            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );

    }

    public function show(){

    }
    public function destroy(){

    }

}
