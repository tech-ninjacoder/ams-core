<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\WorkersProviders;
use App\Services\Tenant\Employee\WorkersProvidersService;
use App\Services\Tenant\WorkingShift\WorkingShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkersProviderstEmployeeController extends Controller
{
    protected WorkingShiftService $workingShiftService;

    public function __construct(WorkersProvidersService $service, WorkingShiftService $workingShiftService)
    {
        $this->service = $service;
        $this->workingShiftService = $workingShiftService;
    }

    public function getEmployees(WorkersProviders $workerProvider) {
        return $workerProvider->users;
    }

    public function update(Request $request)
    {
        validator($request->all(), [
            'worker_provider_id' => 'required|exists:workers_providers,id',
            'users' => 'required|array'
        ])->validate();

        $workerProvider = WorkersProviders::with('employee')->findOrFail($request->get('worker_provider_id'));

        DB::transaction(function() use($workerProvider, $request) {
            $this->service
                ->setModel($workerProvider)
                ->setAttributes($request->only('worker_provider_id', 'users'))
                ->moveEmployee();

        });

        return [
            'status' => true,
            'message' => trans('default.move_response', [
                'subject' => __t('employee'),
                'location' => $workerProvider->name
            ])
        ];
    }
}
