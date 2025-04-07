<?php

namespace App\Http\Controllers\Tenant\Project;

use App\Exports\ExportProjectReport;
use App\Filters\Tenant\ProjectFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Project\ProjectRequest as Request;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Project\ProjectService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Auth\Access\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use phpDocumentor\Reflection\Types\Object_;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class ProjectController extends Controller
{

    public function __construct(ProjectService $service, ProjectFilter $projectFilter)
    {
        $this->service = $service;
        $this->filter = $projectFilter;
    }

    public function index()
    {
        // Get the current authenticated user
        $user = auth()->user();

        // Start building the query
        $query = $this->service
            ->filters($this->filter)
            ->withCount('users')
            ->withCount('managers')
            ->with('status:id,name,class')
            ->with('managers')
            ->with('coordinators')
            ->with('contractors')
            ->with('locations')
            ->with('subdivisions')
            ->with('parent')
            ->with('childrens')
            ->with('gate_passes')
            ->latest('id')
            ->withAggregate('parent', 'project_id')
            ->withAggregate('working_shifts', 'name');

    if (request()->get('project_access_behavior') === 'access_dep_projects') {
        // Get the department IDs the user is managing
        $departmentIds = $user->hasDepartments()->pluck('id');
        Log::info('Department IDs: ', $departmentIds->toArray());

        // Get the user IDs of managers in those departments
        $managerIds = User::whereHas('departments', function ($query) use ($departmentIds) {
            $query->whereIn('departments.id', $departmentIds);
        })->pluck('id');

        Log::info('Manager IDs: ', $managerIds->toArray());

        // Filter projects where the manager's ID is in the list of manager IDs
        $query->whereHas('managers', function ($query) use ($managerIds) {
            $query->whereIn('user_id', $managerIds)
                ->whereNull('project_manager.end_date');
        });
    }elseif (request()->get('project_access_behavior') === 'access_own_projects') {        // Check for project access behavior

            $query->whereHas('managers', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereNull('project_manager.end_date');
            });
        }


        // Paginate the results
        $paginated = $query->paginate(request()->get('per_page', 10));

        // Convert the results to an array
        $response = $paginated->toArray();

        // Set the data in the response
        $response['data'] = $paginated->items();

        return $response;
    }


    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $this->service
                ->setAttributes($request->only('name','pme_id', 'p_start_date', 'p_end_date','status_id', 'location', 'description','est_man_hour', 'lunch_in', 'contractor_id','location_id','subdivision_id'))
                ->save();

            $this->service
//                ->setWorkingShiftDetails($request->details)
//                ->saveDetails()
//                ->when(count($request->get('departments')), function (WorkingShiftService $service) use ($request) {
//                    //$service->assignToDepartments($request->get('departments'));
//                    $service->assignToDepartmentAsUpcoming($request->get('departments'));
//                })->assignToUserAsUpcoming($request->get('users'))
//                //->assignToUsers($request->get('users'))
                ->notify('project_created');

            return created_responses('projects');
        });
    }


    public function show(Project $project)
    {
        $pid = $project->id;
        $project
        ->load([
            'users' => fn($b) => $b
                ->select('users.id', 'users.first_name', 'users.last_name')
//            'upcomingDepartments:id',
//            'upcomingUsers:id'
]
        )
//            ->loadCount('attendances')
        ;

        $users = resolve(DepartmentRepository::class)
//            ->employees($project->departments->pluck('id')->toArray())
            ->pluck('id')
            ->toArray();
        $projects = Project::pluck('id')->toArray();
        $working_shifts = WorkingShift::pluck('id')->toArray();
        $gate_passes = GatePass::pluck('id')->toArray();


        $project->setRelation(
            'users',
            $project->users->filter(fn(User $user) => !in_array($user->id, $users))->values()
        );
        $project->setRelation(
            'managers',
            $project->managers->filter(fn(User $user) => !in_array($user->id, $users))->values()
        );
        $project->setRelation(
            'coordinators',
            $project->coordinators->filter(fn(User $user) => !in_array($user->id, $users))->values()
        );
        $project->setRelation(
            'parent',
            $project->parent->filter(fn(Project $project) => in_array($project->id, $projects))->values()
        );
        $project->setRelation(
            'childrens',
            $project->childrens->filter(fn(Project $project) => in_array($project->id, $projects))->values()
        );
        $project->setRelation(
            'working_shifts',
            $project->working_shifts->filter(fn(WorkingShift $working_shift) => in_array($working_shift->id, $working_shifts))->values()
        );
        $project->setRelation(
            'gate_passes',
            $project->gate_passes->filter(fn(GatePass $gate_pass) => in_array($gate_pass->id, $gate_passes))->values()
        );
//        $project->setRelation(
//            'working_shifts',
//            $project->working_shifts->filter(fn(Project $project) => in_array($project->id, $working_shifts))->values()
//        );
        $project->act_man_hour = Project::project_mh($pid);


//        $project_child = Project::with('parent2')->get();
        Log::info($project);



        return $project;


    }

    public function update(Project $project, Request $request)
    {
        DB::transaction(
            fn() => $this->service
//                ->setIsUpdating(true)
                ->setAttributes($request->only('name','pme_id', 'p_start_date', 'p_end_date', 'location', 'description','est_man_hour', 'lunch_in', 'contractor_id','status_id','location_id','subdivision_id'))
                ->setModel($project)
                ->compareTo($project)
//                ->validateIfAttendanceNotExist('updated')
                ->update()
//                ->setWorkingShiftDetails($request->get('details'))
//                ->saveDetails()
                //->assignToDepartments($request->get('departments', []))
//                ->assignUpdateToDepartmentAsUpcoming($request->get('departments', []))
//                ->assignUpdateToUserAsUpcoming($request->get('users', []))
                ->assignToParent($request->get('parent_project_id', []))
//                ->assignToWorkingShift($request->get('working_shift_id'),[])
                ->notify('project_updated')
        );

        return updated_responses('projects');
    }




    public function destroy(Project $project)
    {
          DB::transaction(
            fn() => $this->service
                ->setModel($project)
                ->endPreviousProjectAdminOfUsers()
//                ->endPreviousWorkingShiftsOfDepartments()
//                ->deleteUpcomingUsersAndDepartment()
                ->delete()
                ->notify('project_deleted')
    );

        return deleted_responses('projects');
    }

    public function addGeometry(Project $project) {
        Log::info('Geometry attached for project:=> '.$project.' //boudgeau');
        return deleted_responses('projects');

    }
    public function projectDetails(Project $project){
        return view('tenan');

    }
    public function ExcelExport($id) {
        $response = (new ExportProjectReport($id))->download('users.csv');
        ob_end_clean();

        return $response;

    }
    public function PDFExport($id) {
//        $project = Project::where('id',$id)
//            ->with('users')
//            ->with('managers')
//            ->with('coordinators')
//            ->get();
        $project = Project::where('id',$id)->with('users','managers','coordinators','parent','childrens','working_shifts','gate_passes')->get(); //get general details about this project
        $visitors = Attendance::whereHas('details', function($q) use ($id) { //get all attendances for this project
            $q->where('project_id', $id);
        })->with('details','profile','user','details.project')
            ->get();
        $project_attendance_log = [];
        $cumulative_time = '00:00:00';
        $cumulative_time = Carbon::parse($cumulative_time)->secondsSinceMidnight();

        foreach ($visitors as  $visitor){ // recalculate daily attendance to know how many hours consumed on this project
            $visit = [];
            $visitor = $visitor->toArray();
            array_push($visit, $visitor);
            $result = Project::calculateAttendanceDaily($visit);
//            $result = json_decode($result[0],false);
//            Log::info($result[0]);
            $total = Carbon::parse($result[0]['total'])->secondsSinceMidnight();
//            Log::info('total=> '.$result[0]['total'].' seconds=>'.Carbon::parse($result[0]['total'])->secondsSinceMidnight().' wrong-calc=> '.$total);
            $project_attendance_log[$result[0]['in_date']][$result[0]['employee_id']] = $result[0]['total'];

            $cumulative_time += $total;
        }
//        Log::info(json_encode($project_attendance_log));

        $seconds = $cumulative_time;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        $history = [];
//        Log::info('visitors=> '.$visitors);


        Log::info($project);
        $pdf = PDF::loadView('tenant.project.full_report',['project'=>$project,'visitors'=>$visitors, 'hours'=>$hours])->setPaper('a4', 'landscape');;

        return $pdf->download('project_full_report.pdf');

    }
}
