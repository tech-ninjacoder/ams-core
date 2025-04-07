<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Filters\Tenant\EmployeeFilter;
use App\Helpers\Traits\AssignRelationshipToPaginate;
use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmployeeRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Services\Core\Auth\UserService;
use App\Services\Tenant\Employee\EmployeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Matrix\Builder;

class EmployeeController extends Controller
{
    use AssignRelationshipToPaginate, DepartmentAuthentications;

    public function __construct(EmployeeService $service, EmployeeFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        $workShift = WorkingShift::getDefault(['id', 'name']);

        if (!request()->get('employment_statuses')) {
            request()->merge(['employment_statuses' => implode(', ', EmploymentStatus::query()
                ->where('alias', '!=', 'terminated')
                ->pluck('id')
                ->toArray())
            ]);
        }

        $paginated = $this->service
            ->filters($this->filter)
            ->with([
                'department:id,name',
                'designation:id,name',
                'provider:id,name',
                'helmet:id,imei,pme_barcode',
                'gate_passes:id,name',
                'profile:id,user_id,joining_date,employee_id',
                'profilePicture',
                'workingShift:id,name',
                'employmentStatus:id,name,class,alias',
                'roles:id,name',
                'skills:id,name',
                'status',
                'projects:id,pme_id,name,location',
                'projects.managers',
                'visits:id,name',
                'updatedSalary',
                'salary',
                'recurringAttendance:id,in_time,out_time',
            ])->where('is_in_employee', 1)
//            ->orderBy('projects','DESC')
            ->orderBy(request()->get('order_by', 'id'),'DESC')
            ->withAggregate('projects','pme_id')
            ->withAggregate('skills','name')
            ->withAggregate('workingShift','name')
            ->withAggregate('department','name')
            ->latest('id')
            ->paginate(request()->get('per_page', 10));

        return $this->paginated($paginated)
            ->setRelation(function (User $user) use ($workShift) {
                if (!$user->workingShift) {
                    $user->setRelation('workingShift', $workShift);
                }
            })->get();
    }

    public function show(User $employee)
    {
         $employee->load([
            'department:id,name',
            'designation:id,name',
            'provider:id,name',
             'helmet:id,imei,pme_barcode',
             'gatePasses:id,name',
             'profile:id,user_id,joining_date,employee_id,gender,date_of_birth,about_me,phone_number',
            'profilePicture',
            'workingShift:id,name',
            'employmentStatus:id,name,class,alias',
            'roles:id,name',
             'skills:id,name',
            'status',
             'projects:id,name',
//             'visits:id,name,start_date,end_date',
             'updatedSalary',
            'salary',

         ]);
        if (!$employee->workingShift) {
            $workShift = WorkingShift::getDefault(['id', 'name', 'is_default']);
            $employee->setRelation('workingShift', $workShift);
        }
        return $employee;
    }

    public function update(EmployeeRequest $request, User $employee)
    {
        $this->departmentAuthentications($employee->id);

        resolve(UserService::class)->validateIsNotDemoVersion();

        DB::transaction(function () use($employee, $request){
            $this->service
                ->setModel($employee)
                ->setAttributes($request->except('allowed_resource', 'tenant_id', 'tenant_short_name'))
                ->update();
        });

        return updated_responses('employee');
    }

    public function destroy(User $employee)
    {
        $this->service
            ->setModel($employee)
            ->delete();

        return deleted_responses('employee');
    }

    public function visits($project)
    {
        $workShift = WorkingShift::getDefault(['id', 'name']);


//        $visits = ProjectUser::where('project_id',5601)->get();
        $paginated = $this->service
            ->filters($this->filter)
            ->with([
                'skills:id,name',
//                'projects:id,pme_id,name,location',
//                'visits:id,name',
//                'attendances.details:in_time'
                'attendances.details'

            ])->where('is_in_employee', 1)
//            ->with(['visits' => function ($query) {
//                $query->where('project_id', '=', 97);
//            }])
                ->whereHas('attendances.details', function($q) use ($project) {
                $q->where('project_id','=', $project);
            })

                ->latest('id')
            ->paginate(request()->get('per_page'));

        return $this->paginated($paginated)->get();    }


    public function export()
    {
        Log::info('export');
        $workShift = WorkingShift::getDefault(['id', 'name']);

        if (!request()->get('employment_statuses')) {
            request()->merge(['employment_statuses' => implode(', ', EmploymentStatus::query()
                ->where('alias', '!=', 'terminated')
                ->pluck('id')
                ->toArray())
            ]);
        }

        $paginated = $this->service
            ->filters($this->filter)
            ->with([
                'department:id,name',
                'designation:id,name',
                'provider:id,name',
                'helmet:id,imei,pme_barcode',
                'gate_passes:id,name',
                'profile:id,user_id,joining_date,employee_id',
                'profilePicture',
                'workingShift:id,name',
                'employmentStatus:id,name,class,alias',
                'roles:id,name',
                'skills:id,name',
                'status',
                'projects:id,pme_id,name,location',
                'projects.managers',
                'visits:id,name',
                'updatedSalary',
                'salary',
            ])->where('is_in_employee', 1)
//            ->orderBy('projects','DESC')
            ->orderBy(request()->get('order_by', 'id'),'DESC')
            ->withAggregate('projects','pme_id')
            ->withAggregate('skills','name')
            ->withAggregate('workingShift','name')
            ->withAggregate('department','name')
            ->latest('id')
            ->paginate(request()->get('per_page', 10));

        return $this->paginated($paginated)
            ->setRelation(function (User $user) use ($workShift) {
                if (!$user->workingShift) {
                    $user->setRelation('workingShift', $workShift);
                }
            })->get();
    }


}
