<?php

namespace App\Services\Tenant\Project;

use App\Helpers\Core\Traits\HasWhen;
use App\Http\Requests\Tenant\Project\ProjectRequest as Request;
use App\Models\Tenant\Employee\DepartmentUser;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectCoordinator;
use App\Models\Tenant\Project\ProjectGatePass;
use App\Models\Tenant\Project\ProjectManager;
use App\Models\Tenant\Project\ProjectParent;
use App\Models\Tenant\Project\ProjectUser;
use App\Models\Tenant\Project\ProjectWorkingShift;
use App\Models\Tenant\WorkingShift\DepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingUserWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Notifications\Tenant\ProjectNotification;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\TenantService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ProjectService extends TenantService
{
    use HasWhen;

    protected array $workingShiftDetails = [];

    protected bool $isUpdating = false;

    protected array $users = [];
    protected array $projects = [];
    protected array $parents = [];
    protected array $working_shifts = [];
    protected array $gate_passes = [];
    protected int $manager = 0;



    protected int $projectId = 0;

    public function __construct(Project $project)
    {
        $this->model = $project;
    }

    public function update(): ProjectService
    {
        $this->model->fill($this->getAttributes())->save();

        return $this;
    }
    public function compareTo(Project $project): ProjectService //added by hasSsan
    {
        $attributes = collect($this->getAttributes())
            ->map(function ($attribute, $key) use ($project) {
                if ($attribute != $project->$key) {
                    return $key = $attribute;
                }
            })->reject(function ($attribute, $key) {
                return !$attribute || in_array($key, ['id', 'created_at', 'updated_at']);
            });
        Log::info($attributes);

        return $this;
    }



    public function assignToUsers($users): ProjectService
    {
        $users = is_array($users) ? $users : func_get_args();

        $users = array_merge($this->users, $users);

        $this->endPreviousProjectAdminOfUsers($users);
//        Log::info('upppp '.json_encode(Project::find($this->getProjectId())->load('working_shifts')));
        $project = Project::find($this->getProjectId())->load('working_shifts');
        if(isset($project['working_shifts'][0])){
            $working_shift = json_decode($project['working_shifts'][0]);
            foreach ($users as $user){
                $upcomingShift = new UpcomingUserWorkingShift();
                $upcomingShift->user_id = $user;
                $upcomingShift->working_shift_id = $working_shift->id;
                $upcomingShift->start_date = Carbon::now()->toDateString();
                $upcomingShift->save();
                Log::info('working shift '.$working_shift->id);
                Log::info('user->id '.$user);

            }
        }

        ProjectUser::insert(
            array_map(
                fn($user) => array_merge([ 'user_id' => $user], self::getCommonPivotColumns()),
                ProjectUser::getNoneExistedUserIds($this->getProjectId(), $users)
            )
        );
        return $this;

    }

    public function assignToParent($parents): ProjectService
    {
        Log::info($parents);
        Log::info($this->getProjectId());

        $parents = is_array($parents) ? $parents : func_get_args();

        $parents = array_merge($this->parents, $parents);

        $this->endPreviousProjectParents($parents);

        ProjectParent::insert(
            array_map(
                fn($parent) => array_merge([ 'parent_id' => $parent], self::getCommonPivotColumns()),
                ProjectParent::getNoneExistedParentIds($this->getProjectId(), $parents)
            )
        );
        //change the status of the group
        $current_project = Project::where('id','=',$this->getProjectId())->first();
        $current_project->type = 0;
        $current_project->save();
        return $this;

    }

    public function assignToWorkingShift($working_shifts): ProjectService
    {
        Log::info($working_shifts);
        Log::info($this->getProjectId());

        $working_shifts = is_array($working_shifts) ? $working_shifts : func_get_args();

//        $working_shifts = array_merge($this->working_shifts, $working_shifts);

        $this->endPreviousProjectWorkingShifts($working_shifts);

        ProjectWorkingShift::insert(
            array_map(
                fn($working_shift) => array_merge([ 'working_shift_id' => $working_shift], self::getCommonPivotColumns()),
                ProjectWorkingShift::getNoneExistedWorkingShiftIds($this->getProjectId(), $working_shifts)
            )
        );
        return $this;

    }

    public function assignToGatesPasses($gate_passes): ProjectService
    {
        Log::info($gate_passes);
//        Log::info($this->getProjectId());

        $gate_passes = is_array($gate_passes) ? $gate_passes : func_get_args();

//        $working_shifts = array_merge($this->working_shifts, $working_shifts);

        $this->endPreviousProjectGatePasses($gate_passes);
        Log::info('store');

        ProjectGatePass::insert(
            array_map(
                fn($gate_pass) => array_merge([ 'gate_passe_id' => $gate_pass], self::getCommonPivotColumns()),
                ProjectGatePass::getNoneExistedGatepass($this->getProjectId(), $gate_passes)
            )
        );
        return $this;

    }

    public function assignManagerToUsers($manager): ProjectService
    {
        $manager = is_array($manager) ? $manager : func_get_args();

//        $users = array_merge($this->$manager, $manager);

        $this->endPreviousProjectManagerOfUsers($manager);

        ProjectManager::insert(
            array_map(
                fn($user) => array_merge([ 'user_id' => $user], self::getCommonPivotColumns()),
                ProjectManager::getNoneExistedUserIds($this->getProjectId(), $manager)
            )
        );
        return $this;

    }
    public function assignCoordinatorToUsers($coordinator): ProjectService
    {
        $coordinator = is_array($coordinator) ? $coordinator : func_get_args();

//        $users = array_merge($this->$manager, $manager);

        $this->endPreviousProjectCoordinatorOfUsers($coordinator);

        ProjectCoordinator::insert(
            array_map(
                fn($user) => array_merge([ 'user_id' => $user], self::getCommonPivotColumns()),
                ProjectCoordinator::getNoneExistedUserIds($this->getProjectId(), $coordinator)
            )
        );
        return $this;

    }
    public function moveEmployee()
    {
        $this->endPreviousProjectAdminOfUsers()
            ->moveToProject();

        return $this;
    }
    public function moveManager()
    {
        $this->endPreviousProjectManagerOfUsers()
            ->moveManagerToProject();

        return $this;
    }

    public function moveWorkingShift()
    {
        $this->endPreviousProjectWorkingShifts()
            ->moveManagerToProject();

        return $this;
    }
    public function moveCoordinator()
    {
        $this->endPreviousProjectCoordinatorOfUsers()
            ->moveManagerToProject();

        return $this;
    }
    public function moveToProject()
    {
        $project_users = collect(ProjectUser::getNoneExistedUsers(
            $this->getProjectId(),
            $this->getAttribute('users')
        ))->map(fn ($user) => [
            'project_id' => $this->getProjectId(),
            'user_id' => $user,
            'start_date' => nowFromApp()->format('Y-m-d')
        ])->toArray();

        ProjectUser::insert($project_users);

//        $departmentWorkingShiftId = DepartmentWorkingShift::getDepartmentWorkingShiftId($this->getDepartmentId()) ?:
//            WorkingShift::getDefault()->id;
//
//        $this->assignUserToDepartmentWorkingShift($departmentWorkingShiftId, $this->getAttribute('users'));

        return $this;
    }

    public function moveManagerToProject()
    {
        $project_users = collect(ProjectManager::getNoneExistedUsers(
            $this->getProjectId(),
            $this->getAttribute('users')
        ))->map(fn ($user) => [
            'project_id' => $this->getProjectId(),
            'user_id' => $user,
            'start_date' => nowFromApp()->format('Y-m-d')
        ])->toArray();

        ProjectManager::insert($project_users);
        return $this;
    }

    public function moveWorkingShiftToProject()
    {
        $project_working_shifts = collect(ProjectWorkingShift::getNoneExistedWorkingShiftIds(
            $this->getProjectId(),
            $this->getAttribute('working_shifts')
        ))->map(fn ($user) => [
            'project_id' => $this->getProjectId(),
            'working_shift_id' => $user,
            'start_date' => nowFromApp()->format('Y-m-d')
        ])->toArray();

        ProjectWorkingShift::insert($project_working_shifts);
        return $this;
    }
    public function moveCoordinatorToProject()
    {
        $project_users = collect(ProjectCoordinator::getNoneExistedUsers(
            $this->getProjectId(),
            $this->getAttribute('users')
        ))->map(fn ($user) => [
            'project_id' => $this->getProjectId(),
            'user_id' => $user,
            'start_date' => nowFromApp()->format('Y-m-d')
        ])->toArray();

        ProjectCoordinator::insert($project_users);
        return $this;
    }


    public function endPreviousProjectAdminOfUsers($users = []): ProjectService
    {
        $removeUser = array_diff($this->model->users->pluck('id')->toArray(), $users);
        if(count($removeUser) && $this->isUpdating){
            ProjectUser::whereNull('end_date')
                ->where('project_id', $this->getProjectId())
                ->whereIn('user_id', $removeUser)
                ->update([
                    'end_date' => nowFromApp()->format('Y-m-d')
                ]);

        }


        ProjectUser::whereNull('end_date')
            ->when(
                $this->isUpdating && !count($users),
                fn (Builder $b) => $b->where('project_id', $this->getProjectId()),
                fn (Builder $b) => $b->where('project_id', '!=', $this->getProjectId())->whereIn('user_id', $users)
            )->update([
                'end_date' => nowFromApp()->format('Y-m-d')
            ]);
        Log::info('$removeUser employee ended on a project');

        return $this;
    }


    public function endPreviousProjectParents($parents = []): ProjectService
    {
        $removeParent = array_diff($this->model->parent->pluck('id')->toArray(), $parents);
//        Log::info('parents array '.json_encode($removeParent));
        if(count($removeParent)){
            ProjectParent::whereNull('end_date')
                ->where('project_id', $this->getProjectId())
                ->whereIn('parent_id', $removeParent)
                ->update([
                    'end_date' => nowFromApp()->format('Y-m-d')
                ]);
            Log::info('dup1');

        }


//        ProjectParent::whereNull('end_date')
//            ->when(
//                $this->isUpdating && count($parents),
//                fn (Builder $b) => $b->where('project_id', $this->getProjectId()),
//                fn (Builder $b) => $b->where('project_id', '!=', $this->getProjectId())->whereIn('parent_id', $parents)
//            )->update([
//                'end_date' => nowFromApp()->format('Y-m-d')
//            ]);
        Log::info('$removeParent Parent ended on a project');

        return $this;
    }

    public function endPreviousProjectWorkingShifts($working_shifts = []): ProjectService
    {
        Log::info('$working_shifts = []: '.json_encode($working_shifts));
        Log::info('$this->model->working_shifts()->pluck() '.json_encode($this->model->working_shifts()->pluck('id')->toArray()));

//        Log::info(json_encode('working shifts to remove from project => '.$working_shifts));
        $removeWorkingShift = array_diff($this->model->working_shifts()->pluck('id')->toArray(), $working_shifts);
        Log::info('$removeWorkingShift count==> '.json_encode($removeWorkingShift));

//        Log::info('parents array '.json_encode($removeParent));
        if(count($removeWorkingShift)){
            ProjectWorkingShift::whereNull('end_date')
                ->where('project_id', $this->getProjectId())
                ->whereIn('working_shift_id', $removeWorkingShift)
                ->update([
                    'end_date' => nowFromApp()->format('Y-m-d')
                ]);
//            Log::info('dup1');

        }
        Log::info('remove gate pass past Parent ended on a project');

        return $this;
    }
    public function endPreviousProjectGatePasses($gate_passes = []): ProjectService
    {
        Log::info('$gate_passes = []: '.json_encode($gate_passes));
        Log::info('$this->model->gate_passes()->pluck() '.json_encode($this->model->gate_passes()->pluck('id')));

//        Log::info(json_encode('working shifts to remove from project => '.$working_shifts));
        $removeGatePass = array_diff($this->model->gate_passes()->pluck('id')->toArray(), $gate_passes);
        Log::info('count==> '.json_encode($removeGatePass));
//        Log::info('parents array '.json_encode($removeParent));
        if(count($removeGatePass)){
            ProjectGatePass::whereNull('end_date')
                ->where('project_id', $this->getProjectId())
                ->whereIn('gate_passe_id', $removeGatePass)
                ->update([
                    'end_date' => nowFromApp()->format('Y-m-d')
                ]);
            Log::info('dup1');

        }
        Log::info('remove gate pass past Parent ended on a project');

        return $this;
    }

    public function endPreviousProjectManagerOfUsers($manager): ProjectService
    {
//        $removeUser = array_diff($this->model->users->pluck('id')->toArray(), $users);
//        if(count($removeUser) && $this->isUpdating){
            ProjectManager::whereNull('end_date')
                ->where('project_id', $this->getProjectId())
//                ->whereIn('user_id', $removeUser)
                ->update([
                    'end_date' => nowFromApp()->format('Y-m-d')
                ]);

//        }

//        ProjectManager::whereNull('end_date')
//            ->when(
//                $this->isUpdating && !count($users),
//                fn (Builder $b) => $b->where('project_id', $this->getProjectId()),
//                fn (Builder $b) => $b->where('project_id', '!=', $this->getProjectId())->whereIn('user_id', $users)
//            )->update([
//                'end_date' => nowFromApp()->format('Y-m-d')
//            ]);
        Log::info('$removeUser employee ended on a project');

        return $this;
    }
    public function endPreviousProjectCoordinatorOfUsers($coordinator): ProjectService
    {
        ProjectCoordinator::whereNull('end_date')
            ->where('project_id', $this->getProjectId())
            ->update([
                'end_date' => nowFromApp()->format('Y-m-d')
            ]);
        Log::info('$removeUser employee ended on a project');

        return $this;
    }


    public function notify($event, $project = null): ProjectService
    {
        $project = $project ?: $this->model;

        notify()
            ->on($event)
            ->with($project)
            ->send(ProjectNotification::class);

        return $this;
    }


    public function getCommonPivotColumns()
    {
        return [
            'project_id' => $this->getProjectId(),
            'start_date' => todayFromApp()->format('Y-m-d'),
            'end_date' => null
        ];
    }

    public function setUsers(array $users): ProjectService
    {
        $this->users = $users;
        return $this;
    }

    public function setProjectId(int $projectId): ProjectService
    {
        $this->projectId = $projectId;

        return $this;
    }

    public function getProjectId(): int
    {
        return $this->projectId ?: $this->model->id;
    }

    public function validateUsers()
    {
        validator($this->getAttributes(), [
            'users' => 'required|array'
        ]);

        return $this;
    }
    public function setIsUpdating(bool $isUpdating): ProjectService
    {
        $this->isUpdating = $isUpdating;
        return $this;
    }

}
