<?php


namespace App\Helpers\Traits;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Project\Project;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait UserAccessQueryHelper
{
    public function userAccessQuery($builder, $key = 'user_id', $withAuth = true){
        /** @var User $user */
        $user = auth()->user();

        $deptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers($user->id);
        Log::info('userAccessQuery');
        Log::info('builder '.json_encode($builder));
        Log::info('$deptUsers '.json_encode($deptUsers));

        $builder->where(fn(Builder $builder) => $builder
            ->whereIn($key, $deptUsers)
            ->when($withAuth, fn(Builder $b) => $b->orWhere($key, auth()->id()))
        );
    }
    public function enginnerAccessQuery($builder, $key = 'user_id', $withAuth = true){
        /** @var User $user */
        $user = auth()->user();
        $managedProjectIds = $user->managedProjects->pluck('id');
        Log::info('enginnerAccessQuery');

        Log::info('$managedProjects'.json_encode($managedProjectIds));


        $deptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers($user->id);


        // Get users working on the managed projects
        $usersWorkingOnManagedProjects = User::whereHas('projects', function ($query) use ($managedProjectIds) {
            $query->whereIn('projects.id', $managedProjectIds);
        })->get();
        Log::info('$usersWorkingOnManagedProjects '.json_encode($usersWorkingOnManagedProjects));
        $projUsers =$usersWorkingOnManagedProjects->pluck('id');
        Log::info('$projUsers '.json_encode($projUsers));



        $builder->where(fn(Builder $builder) => $builder
            ->whereIn($key, $projUsers)
            ->when($withAuth, fn(Builder $b) => $b->orWhere($key, auth()->id()))
        );

    }

    public function projectAttendanceAccessQuery($builder, $key = 'user_id', $withAuth = true){
        /** @var User $user */
        $user = auth()->user();
        $requestDate = request()->date;

        $managedProjectIds = $user->managedProjectsWithinDate($requestDate)->pluck('id');
        Log::info('projectAttendanceAccessQuery');
        Log::info('date: '.request()->date);

        Log::info('$managedProjects'.json_encode($managedProjectIds));


//        $deptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers($user->id);


        // Get users working on the managed projects
        $usersWorkingOnManagedProjects = User::whereHas('projects', function ($query) use ($managedProjectIds) {
            $query->whereIn('projects.id', $managedProjectIds);
        })->get();
        Log::info('$usersWorkingOnManagedProjects '.json_encode($usersWorkingOnManagedProjects));
        $projUsers = $usersWorkingOnManagedProjects->pluck('id')->toArray();
        $dummy = [938,1130,934,948,1227,1208] ;
        Log::info('$projUsers '.json_encode($projUsers));



        $builder->where(fn(Builder $builder) => $builder
            ->whereIn($key, $projUsers)
            ->when($withAuth, fn(Builder $b) => $b->orWhere($key, auth()->id()))
        );
        Log::info('$builder '.json_encode($builder));

    }
}
