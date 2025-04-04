<?php

namespace App\Http\Controllers\Tenant\Dashboard;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\Helper\DashboardUserAccessQueryFilter;
use App\Filters\Tenant\Helper\DashboardWhereHasUserAccessQueryFilter;
use App\Filters\Tenant\Helper\DepartmentAccessFilter;
use App\Filters\Tenant\Helper\UserAccessFilter;
use App\Filters\Tenant\Helper\WhereHasEmployeesAccessFilter;
use App\Filters\Tenant\Helper\WhereHasUserAccessFilter;
use App\Filters\Tenant\Helper\WhereHasUsersAccessFilter;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Employee\Helmet;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\Skill;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Project\Location;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\Subdivision;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Dashboard\AdminDashboardService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    use UserAccessQueryHelper;
    /**
     * @var DepartmentAccessFilter
     */
    private DepartmentAccessFilter $departmentFilter;
    /**
     * @var WhereHasUserAccessFilter
     */
    private WhereHasUserAccessFilter $whereHasUserFilter;
    /**
     * @var UserAccessFilter
     */
    private UserAccessFilter $userFilter;

    private DashboardUserAccessQueryFilter $dashboardUserAccessQueryFilter;
    /**
     * @var WhereHasUsersAccessFilter
     */
    private WhereHasUsersAccessFilter $whereHasUsersAccessFilter;
    /**
     * @var WhereHasEmployeesAccessFilter
     */
    private WhereHasEmployeesAccessFilter $whereHasEmployeesAccessFilter;

    private DashboardWhereHasUserAccessQueryFilter $dashboardWhereHasUserAccessQueryFilter;

    public function __construct(
        AdminDashboardService          $service,
        UserAccessFilter               $userFilter,
        DepartmentAccessFilter         $departmentFilter,
        WhereHasUserAccessFilter       $whereHasUserFilter,
        WhereHasUsersAccessFilter      $whereHasUsersAccessFilter,
        WhereHasEmployeesAccessFilter  $whereHasEmployeesAccessFilter,
        DashboardUserAccessQueryFilter $dashboardUserAccessQueryFilter,
        DashboardWhereHasUserAccessQueryFilter $dashboardWhereHasUserAccessQueryFilter
    )
    {
        $this->service = $service;
        $this->userFilter = $userFilter;
        $this->departmentFilter = $departmentFilter;
        $this->whereHasUserFilter = $whereHasUserFilter;
        $this->whereHasUsersAccessFilter = $whereHasUsersAccessFilter;
        $this->whereHasEmployeesAccessFilter = $whereHasEmployeesAccessFilter;
        $this->dashboardUserAccessQueryFilter = $dashboardUserAccessQueryFilter;
        $this->dashboardWhereHasUserAccessQueryFilter = $dashboardWhereHasUserAccessQueryFilter;
    }

    public function summery(): array
    {
        $employees = User::query()->filters($this->dashboardUserAccessQueryFilter)
            ->where('is_in_employee', 1)->count();
        $departments = Department::query()->filters($this->departmentFilter)->count();
        $projects = Project::query()->filters($this->departmentFilter)->count(); //boudgeau
        $helmets = Helmet::query()->filters($this->departmentFilter)->count(); //boudgeau
        $gate_pass = GatePass::query()->filters($this->departmentFilter)->count(); //boudgeau



        [$leave_pending, $leave_approved] = resolve(StatusRepository::class)->leavePendingApproved();
        $leave_requests = Leave::query()->filters($this->dashboardWhereHasUserAccessQueryFilter)
            ->where('status_id', $leave_pending)->count();

        $on_leave_today = Leave::query()->filters($this->dashboardWhereHasUserAccessQueryFilter)
            ->where('status_id', $leave_approved)
            ->whereDate('start_at', '>=', todayFromApp())
            ->whereDate('end_at', '<=', todayFromApp())
            ->groupBy('user_id')->count();

        return [
            'total_employee' => auth()->user()->can('view_employees') ? $employees : 0,
            'total_department' => auth()->user()->can('view_departments') ? $departments : 0,
            'total_projects' => auth()->user()->can('view_departments') ? $projects : 0, //boudgeau
            'total_helmets' => auth()->user()->can('view_departments') ? $helmets : 0, //boudgeau
            'total_gate_passes' => auth()->user()->can('view_departments') ? $gate_pass : 0, //boudgeau
            'total_leave_request' => auth()->user()->can('view_all_leaves') ? $leave_requests : 0,
            'on_leave_today' => auth()->user()->can('view_all_leaves') ? $on_leave_today : 0,
        ];
    }

    public function onWorking(): array
    {
        $attendances = Attendance::query()->filters($this->dashboardWhereHasUserAccessQueryFilter)
            ->whereDate('in_date', '=', todayFromApp())->get();
        $attendancesStats = $attendances->countBy(fn(Attendance $attendance) => $attendance->behavior);

        return [
            'total' => $attendances->count(),
            'behaviour' => [
                'early' => Arr::get($attendancesStats, 'early') ?: 0,
                'late' => Arr::get($attendancesStats, 'late') ?: 0,
                'regular' => Arr::get($attendancesStats, 'regular') ?: 0,
            ]
        ];
    }

    public function employeeStatistics()
    {
        if (\request()->get('key') === 'by_employment_status' && auth()->user()->can('view_employment_statuses')) {
            return EmploymentStatus::filters($this->whereHasEmployeesAccessFilter)
                ->get()
                ->flatMap(function (EmploymentStatus $status) {
                    $statusCounts = [
                        $status->name => $status->employees()
                            ->filters($this->dashboardUserAccessQueryFilter)
                            ->whereNull('end_date')
                            ->where('is_in_employee', 1)
                            ->count()
                    ];

// Filter out zero count values
                    $statusCounts = array_filter($statusCounts, function($value) {
                        return $value > 0;
                    });

                    return $statusCounts;

                });
        }
        if (\request()->get('key') === 'by_designation' && auth()->user()->can('view_designations')) {
            return Designation::filters($this->whereHasUsersAccessFilter)
                ->get()
                ->flatMap(function (Designation $designation) {
                return [
                    $designation->name => $designation->users()
                        ->filters($this->dashboardUserAccessQueryFilter)                        ->whereNull('end_date')
                        ->where('is_in_employee', 1)
                        ->count()
                ];
            });
        }
        if (\request()->get('key') === 'by_skills' && auth()->user()->can('view_designations')) {
            return Skill::filters($this->whereHasUsersAccessFilter)
                ->get()
                ->flatMap(function (Skill $skill) {
                    return [
                        $skill->name => $skill->users()
                            ->filters($this->dashboardUserAccessQueryFilter)                        ->whereNull('end_date')
                            ->where('is_in_employee', 1)
                            ->count()
                    ];
                });
        }

        if (\request()->get('key') === 'by_projects' && auth()->user()->can('view_designations')) {
            return Project::filters($this->whereHasUsersAccessFilter)
                ->get()
                ->flatMap(function (Project $project) {
                    $projectCounts = [
                        $project->name => $project->users()
                            ->filters($this->dashboardUserAccessQueryFilter)
                            ->whereNull('end_date')
                            ->where('is_in_employee', 1)
                            ->count()
                    ];

// Filter out zero count values
                    $projectCounts = array_filter($projectCounts, function($value) {
                        return $value > 0;
                    });

                    return $projectCounts;

                });
        }
        if (\request()->get('key') === 'by_department' && auth()->user()->can('view_departments')) {
            return Department::filters($this->whereHasUsersAccessFilter)->get()->flatMap(function (Department $department) {
                return [$department->name => $department->users()
                    ->filters($this->dashboardUserAccessQueryFilter)                    ->whereNull('end_date')
                    ->where('is_in_employee', 1)
                    ->count()
                ];
            });
        }
        if (\request()->get('key') === 'by_status' && auth()->user()->can('view_employment_statuses')) {
            $result = User::where('is_in_employee',1)
            ->join('provider_user', 'users.id', '=', 'provider_user.user_id')
                ->join('providers', 'provider_user.provider_id', '=', 'providers.id')
                ->join('user_employment_status', 'users.id', '=', 'user_employment_status.user_id')
                ->join('employment_statuses', 'user_employment_status.employment_status_id', '=', 'employment_statuses.id')
                ->whereNotIn('employment_statuses.name',['cancelled','terminated'])
                ->whereNull('provider_user.end_date') // Added condition for provider_user table
                ->whereNull('user_employment_status.end_date') // Added condition for user_employment_status table
                ->select(
                    DB::raw('CONCAT( employment_statuses.name, " - ", providers.contract_type) as merged_field'),
                    DB::raw('COUNT(users.id) as user_count')
                )
                ->groupBy('merged_field') // Group by the merged field
                ->get();
            $formattedResult = collect($result)->mapWithKeys(function ($item) {
                return [$item->merged_field => $item->user_count];
            })->all();


            return $formattedResult;
//            return EmploymentStatus::filters($this->whereHasEmployeesAccessFilter)
//                ->get()
//                ->flatMap(function (EmploymentStatus $status) {
//                    $statusCounts = $status->employees()
//                        ->filters($this->dashboardUserAccessQueryFilter)
//                        ->whereNull('end_date')
//                        ->where('is_in_employee', 1)
//                        ->with('provider') // Load the provider relationship
//                        ->get()
//                        ->groupBy(function ($employee) use ($status) {
//                            // Combine the status name and contract type for grouping
//                            return $status->name . ' - ' . ($employee->provider->contract_type ?? 'No Contract Type');
//                        })
//                        ->map(function ($groupedEmployees) {
//                            return $groupedEmployees->count(); // Count employees in each group
//                        })
//                        ->filter(function ($count) {
//                            return $count > 0; // Filter out groups with a count of 0
//                        });
//
//                    return $statusCounts;
//                });
        }


        throw new GeneralException('can_not_fetch_data');

    }
    public function ProjectStatistics()
    {

        if (\request()->get('key') === 'by_projects' && auth()->user()->can('view_designations')) {
            return Project::filters($this->whereHasUsersAccessFilter)
                ->get()
                ->flatMap(function (Project $project) {
                    return [
                        $project->name => $project->users()
                            ->filters($this->dashboardUserAccessQueryFilter)                        ->whereNull('end_date')
                            ->where('is_in_employee', 1)
                            ->count()
                    ];
                });
        }
        if (\request()->get('key') === 'by_department' && auth()->user()->can('view_departments')) {
            return Department::filters($this->whereHasUsersAccessFilter)->get()->flatMap(function (Department $department) {
                return [$department->name => $department->users()
                    ->filters($this->dashboardUserAccessQueryFilter)                    ->whereNull('end_date')
                    ->where('is_in_employee', 1)
                    ->count()
                ];
            });
        }

        throw new GeneralException('can_not_fetch_data');

    }
    public function LaborSupply(){
        $paginated = User::select('first_name','last_name','status_id')
            ->where('is_in_employee', 1)
//            ->orderBy('projects','DESC')
            ->orderBy(request()->get('order_by', 'id'),'DESC')
            ->withAggregate('profiles','employee_id')
            ->withAggregate('projects','pme_id')
            ->withAggregate('projects','location')
            ->withAggregate('projects','location_id')
            ->withAggregate('projects','subdivision_id')
            ->withAggregate('provider','name')
            ->withAggregate('skills','name')
            ->withAggregate('workingShift','name')
            ->withAggregate('department','name')
            ->withAggregate('employmentStatus','name')
            ->latest('id')->get();
// Loop through each user's projects and fetch the location name
        foreach ($paginated as $user) {
//            Log::info(json_encode($user->projects_location));
            $location  = Location::find($user->projects_location_id); // Assuming 'name' is the attribute name for location name
            $subdivision = Subdivision::find($user->projects_subdivision_id);
            $locationName = $location ? $location->name : 'Unknown Location';
            $subdivisionName = $subdivision ? $subdivision->name : 'Unknown Subdivision';

            $user->setAttribute('location_name', $locationName);
            $user->setAttribute('subdivision_name', $subdivisionName);

        }
        return $paginated;
    }

    public function ProjectPivot () {

        $rawAttendance =  Attendance::where('in_date',Carbon::today())->with('details','profile','user','details.project')->get();
        $newarr = [];
        $CalculatedAttendance = Project::calculateAttendanceDaily($rawAttendance);
        foreach ($CalculatedAttendance as $attendance){
            $temparr = [];
//            Log::info($attendance['ams_project_id']);
            $proj = Project::find($attendance['ams_project_id']);
            $emp = User::where('id','=',$attendance['user_id'])
                ->withAggregate('provider','name')
                ->withAggregate('skills','name')
                ->withAggregate('department','name')->first();
//            Log::info($emp);
//            Log::info('$proj=> '.json_encode($proj));
            $temparr = $attendance;
            if($proj){
                $temparr['project_location_details'] = $proj->location;
                $temparr['project_location_id'] = $proj->location_id; //
                $temparr['project_subdivision_id'] = $proj->subdivision_id; //


            }else{
                $temparr['project_location_details'] = null;
                $temparr['project_location_id'] = null;
                $temparr['project_subdivision_id'] = null;
            }
            $int_total = Carbon::createFromFormat('H:i:s', $attendance['total']);
            $temparr['int_total'] = intval($int_total->format('G'));
            $temparr['provider']=$emp->provider_name;
            $temparr['skill'] = $emp->skills_name;
            $temparr['camp'] = $emp->department_name;
            array_push($newarr,$temparr);
//            Log::info($proj);
        }

        foreach ($newarr as $key => $user) {
            $location  = Location::find($user['project_location_id']); // Assuming 'name' is the attribute name for location name
            $subdivision = Subdivision::find($user['project_subdivision_id']);


            $locationName = $location ? $location->name : 'Unknown Location';
            $subdivisionName = $subdivision ? $subdivision->name : 'Unknown Subdivision';

            $newarr[$key]['location_name'] = $locationName;
            $newarr[$key]['subdivision_name'] = $subdivisionName;

        }




        return $newarr;

    }
}
