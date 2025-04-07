<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exports\ExportEmployees;
use App\Filters\Tenant\EmployeeFilter;
use App\Helpers\Traits\AssignRelationshipToPaginate;
use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmployeeRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Core\Auth\UserService;
use App\Services\Tenant\Employee\EmployeeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Matrix\Builder;

class EmployeeExportController extends Controller
{
    use AssignRelationshipToPaginate, DepartmentAuthentications;

    public function __construct(EmployeeService $service, EmployeeFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function export()
    {
        Log::info('export export');
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
            ->select('first_name','last_name')
            ->where('is_in_employee', 1)
//            ->orderBy('projects','DESC')
            ->orderBy(request()->get('order_by', 'id'),'DESC')
            ->withAggregate('profile','employee_id')
            ->withAggregate('projects','pme_id')
            ->withAggregate('projects','location')
            ->withAggregate('provider','name')
            ->withAggregate('skills','name')
            ->withAggregate('employmentStatus','name')
//            ->withAggregate('workingShift','name')
//            ->withAggregate('projects.working_shifts','name')

            ->withAggregate('department','name')
            ->withAggregate('projects','id')

            ->latest('id')->get();
        Log::info('$paginated '.json_encode($paginated));

        foreach ($paginated as $user){
//            Log::info('$user'.$user->projects_id);
            if(!is_null($user->projects_id)){
            $project = Project::find($user->projects_id);
            $workShift = $project->WorkingShifts;
            $coordinators = $project->coordinators;
            Log::info('$coordinators '.json_encode($coordinators));
//            Log::info('relaton '.$workShift[0]['name']);
            if (count($workShift) > 0) {
                $user->workShiftName = $workShift[0]['name'];
                // Assuming you have added a 'workShiftName' attribute to your User model.
                // You can replace 'workShiftName' with any attribute name you prefer.
            } else {
                $user->workShiftName = null; // No work shift available.
            }

            //add coordinator
            if (count($coordinators) > 0) {
                $user->coordinatorName = $coordinators[0]['name'];
                // Assuming you have added a 'coordinatorName' attribute to your User model.
                // You can replace 'coordinatorName' with any attribute name you prefer.
            } else {
                $user->coordinatorName = "N/A"; // No coordinator available.
            }

            //
            }else{
                $user->workShiftName = "N/A";
                $user->coordinatorName = "N/A";

            }



        }
        Log::info('jsooon=>'.json_encode($paginated));

//        $file_name = 'employees_'.date('Y_m_d_H_i_s').'.csv';
//        return Excel::download(new EmployeesExport, $file_name);


        $now = Carbon::now();
        $todayDate = $now->toDateString(); // Get today's date in the 'Y-m-d' format

        $fileName = "pme_distribution_$todayDate.xlsx";

        return Excel::download(new ExportEmployees($paginated->toArray()), $fileName);


    }

    public function index()
    {
        Log::info('export index');
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
            ->select('first_name','last_name')
            ->where('is_in_employee', 1)
//            ->orderBy('projects','DESC')
            ->orderBy(request()->get('order_by', 'id'),'DESC')
            ->withAggregate('profiles','employee_id')
            ->withAggregate('projects','pme_id')
            ->withAggregate('projects','location')
            ->withAggregate('provider','name')
            ->withAggregate('skills','name')
            ->withAggregate('workingShift','name')
            ->withAggregate('department','name')
            ->latest('id')->get();
//        $file_name = 'employees_'.date('Y_m_d_H_i_s').'.csv';
//        return Excel::download(new EmployeesExport, $file_name);


        return $paginated;


    }

    public function print_distribution(){
        $user = auth()->user();
        Log::info('user auth '.$user);
        $departments = Department::where('manager_id',$user['id'])->where('status_id',4)->pluck('id');
        Log::info('$department '.$departments);

        $projects = ProjectUser::where('end_date',null)->pluck('project_id');
        $uniqueProjects = collect($projects)->unique()->values()->all();

        $active_projects = Project::whereIn('id', $uniqueProjects)
            ->withAggregate('coordinators','first_name')
            ->withAggregate('coordinators','last_name')

            ->get();

        foreach ($active_projects as $project){
            $project
                ->load([
                        'users' => fn($b) => $b
                            ->select('users.id', 'users.first_name', 'users.last_name')
                            ->withAggregate('profile','employee_id')
                            ->whereHas('employmentStatuses', function ($query) {
                                $query->where('employment_status_id', 2)
                                    ->whereNull('end_date');
                            })
                            ->whereHas('departments', function ($query) use ($departments){
                                $query->wherein('id', $departments)
                                    ->whereNull('end_date');
                            })



            ]);
        }
        // Remove projects without users
        $active_projects = $active_projects->filter(function ($project) {
            return $project->users->isNotEmpty();
        });
//        Log::info('count $active_projects=> '.count($active_projects));
//        foreach ($active_projects as $proj){
//            Log::info('count $pme_id=> '.$proj->pme_id);
//
//        }

        $pdf = PDF::loadView('tenant.project.distribution_report',['project'=>$active_projects])->setPaper('a4', 'landscape');;

        return $pdf->download('project_full_report.pdf');

//        return response()->json($active_projects);
    }

}
