<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Filters\Tenant\DepartmentFilter;
use App\Filters\Tenant\WorkersProvidersFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\DepartmentRequest;
use App\Http\Requests\Tenant\Employee\WorkersProvidersRequest;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\WorkersProviders;
use App\Models\Tenant\WorkingShift\UpcomingDepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Employee\DepartmentService;
use App\Services\Tenant\Employee\WorkersProvidersService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkersProvidersControllers extends Controller
{
    public function __construct(WorkersProvidersService $service, WorkersProvidersFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
//            ->with('')
            ->latest('id')
            ->paginate(request()->get('per_page', 10));
    }

    public function store(WorkersProvidersRequest $request)
    {
        Log::info('provder=====> '.$request);
        $workerprovider = DB::transaction(function () use ($request) {

            $attributes = array_merge($request
                ->only('name', 'description','contract_type')
            );

            $workerprovider = $this->service
                ->setAttributes($attributes)
                ->save();
            $this->service
                ->setModel($workerprovider)
                ->notify('department_created');

            return $workerprovider;
        });

        return created_responses('workers_providers', ['workers_providers' => $workerprovider]);
    }

    public function show($workerprovider)
    {
        $workerprovider = WorkersProviders::where('id',$workerprovider)->first();



        return $workerprovider;
    }

    public function update(WorkersProviders $workerprovider, WorkersProvidersRequest $request)
    {
        $workerprovider = DB::transaction(function () use ($request, $workerprovider) {
            $this->service
                ->setAttributes($request
                    ->only('name', 'description','contract_type'))
                ->setModel($workerprovider)
                ->update()
                ->setIsUpdate(true);
            // ->assignWorkingShift();
        });

        return updated_responses('department', ['department' => $workerprovider]);
    }

    public function destroy(WorkersProviders $workerprovider)
    {
        $this->service
            ->setModel($workerprovider)
            ->delete()
            ->notify('department_deleted', (object)$workerprovider->toArray());

        return deleted_responses('department');
    }


}
