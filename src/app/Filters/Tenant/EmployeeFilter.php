<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\CreatedByFilter;
use App\Filters\Core\UserFilter;
use App\Filters\Traits\DateRangeFilter;
use App\Helpers\Traits\MakeArrayFromString;
use App\Helpers\Traits\UserAccessQueryHelper;
use Aws\LookoutEquipment\LookoutEquipmentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeFilter extends UserFilter
{
    use DateRangeFilter, CreatedByFilter, UserAccessQueryHelper;

    use MakeArrayFromString;

    public function designations($designations = null): void
    {
        $designations = $this->makeArray($designations);

        $this->builder->when(count($designations), function (Builder $builder) use ($designations) {
            $builder->whereHas(
                'designation',
                fn(Builder $b) => $b->whereIn('id', $designations)
            );
        });
    }
    public function helmets($helmets = null): void
    {

        $helmets = $this->makeArray($helmets);


        $this->builder->when(count($helmets), function (Builder $builder) use ($helmets) {
            $builder->whereHas(
                'helmet',
                fn(Builder $b) => $b->whereIn('id', $helmets)
            );
        });
    }

    public function recurringAttendance($ra = null): void
    {
        Log::info('recurring_attendance filter');

        $ra = $this->makeArray($ra);


        $this->builder->when(count($ra), function (Builder $builder) use ($ra) {
            $builder->whereHas(
                'recurringAttendance',
                fn(Builder $b) => $b->whereIn('id', $ra)
            );
        });
    }
//    public function gate_passes($gate_passes = null): void
//    {
//        Log::info('empfiler');
//
//        $gate_passes = $this->makeArray($gate_passes);
//
//        $this->builder->when(count($gate_passes), function (Builder $builder) use ($gate_passes) {
//            $builder->whereHas(
//                'gate_pass',
//                fn(Builder $b) => $b->whereIn('id', $gate_passes)
//            );
//        });
//    }
    public function providers($providers = null): void
    {
        $providers = $this->makeArray($providers);

        $this->builder->when(count($providers), function (Builder $builder) use ($providers) {
            $builder->whereHas(
                'provider',
                fn(Builder $b) => $b->whereIn('id', $providers)
            );
        });
    }

    public function departments($departments = null): void
    {
        $departments = $this->makeArray($departments);

        $this->builder->when(count($departments), function (Builder $builder) use ($departments) {
            $builder->whereHas(
                'department',
                fn(Builder $b) => $b->whereIn('id', $departments)
            );
        });
    }

    public function workingShifts($workingShifts = null): void
    {
        $workingShifts = $this->makeArray($workingShifts);

        $this->builder->when(count($workingShifts), function (Builder $builder) use ($workingShifts) {
            $builder->whereHas(
                'workingShift',
                fn(Builder $b) => $b->whereIn('id', $workingShifts)
            );
        });
    }

    public function employmentStatuses($employmentStatus = null): void
    {
        $employmentStatus = $this->makeArray($employmentStatus);

        $this->builder->when(count($employmentStatus), function (Builder $builder) use ($employmentStatus) {
            $builder->whereHas(
                'employmentStatus',
                fn(Builder $b) => $b->whereIn('id', $employmentStatus)
            );
        });
    }

    public function roles($roles = null): void
    {
        $roles = $this->makeArray($roles);

        $this->builder->when(count($roles), function (Builder $builder) use ($roles) {
            $builder->whereHas(
                'roles',
                fn(Builder $b) => $b->whereIn('id', $roles)
            );
        });
    }
    public function skills($skills = null): void
    {
        $skills = $this->makeArray($skills);

        $this->builder->when(count($skills), function (Builder $builder) use ($skills) {
            $builder->whereHas(
                'skills',
                fn(Builder $b) => $b->whereIn('id', $skills)
            );
        });
    }
    public function gatePasses($gate_passes = null): void
    {
        Log::info('gp');
        $gate_passes = $this->makeArray($gate_passes);

        $this->builder->when(count($gate_passes), function (Builder $builder) use ($gate_passes) {
            $builder->whereHas(
                'gate_pass',
                fn(Builder $b) => $b->whereIn('id', $gate_passes)
            );
        });
    }
    public function hasHelmet($has_helmet = null): void
    {
//        $has_helmet = $has_helmet;
//        $has_helmet =1;
        if($has_helmet == 1) {
            $this->builder->whereHas(
                    'helmets',
                    fn(Builder $b) => $b->where('end_date',null)
                );
        } elseif ($has_helmet == 0) {
            $this->builder->
            whereDoesntHave(
                    'helmets',
                    fn(Builder $b) => $b->where('end_date',null)
                );
        }


    }

    public function projects($projects = null): void
    {
        $projects = $this->makeArray($projects);

        $this->builder->when(count($projects), function (Builder $builder) use ($projects) {
            $builder->whereHas(
                'projects',
                fn(Builder $b) => $b->whereIn('id', $projects)->where('end_date',null)
            );
        });
        Log::info($projects);
    }

    public function visits($visits = null): void
    {
        $visits = $this->makeArray($visits);

        $this->builder->when(count($visits), function (Builder $builder) use ($visits) {
            $builder->whereHas(
                'visits',
                fn(Builder $b) => $b->whereIn('id', $visits)

            );
            Log::info($visits[0]);

        });
    }

    public function joiningDate($date = null): void
    {
        $date = json_decode(htmlspecialchars_decode($date), true);

        $this->builder->when($date, function (Builder $builder) use ($date) {
            $builder->whereHas(
                'profile',
                fn(Builder $b) => $b->whereBetween(DB::raw('DATE(joining_date)'), array_values($date))
            );
        });
    }

//    public function all($all = 'yes'): void
//    {
//        $this->builder->when($all == 'no', function (Builder $builder) {
//            $builder->where('id', auth()->id());
//        });
//    }

    public function showAll($showAll = 'yes')
    {
        $user = auth()->user();
        $user_role = $user->load('roles');
        Log::info('EmployeeFilter.php showAll');

        Log::info('$user_role '.$user_role->roles);
        $engineer = null;
        foreach ($user_role->roles as $role){
            Log::info('role=>'.$role);
            if($role->alias === 'project_engineer'){
                Log::info('project engineer');
                $engineer = true;
            }else{
                Log::info('Not project engineer');
                $engineer = false;
            }
        }


        $this->builder->when($showAll == 'no', function (Builder $builder) use ($engineer) {
            $builder->where('id', auth()->id());
        }, function (Builder $builder) use ($engineer){
            $builder->when(request()->get('access_behavior') == 'own_departments', function (Builder $b) use ($engineer)  {
                return $b->when($engineer,
                    fn(Builder $b) => $this->enginnerAccessQuery($b, 'id'),
                    fn(Builder $b) => $this->userAccessQuery($b, 'id')
                );
            });
        });

    }

    //filter employees that are assigned to a project or no
    public function available($free= null){
        Log::info('yessss'.$free);
        $this->builder->when($free == 'assigned', function (Builder $builder) {
            $builder->whereHas('projects');
        }, function (Builder $builder){
            $builder->whereDoesntHave('projects');

        });
    }

    public function gender($gender = null)
    {
        $genders = $this->makeArray($gender);
        $this->builder->when($gender, function (Builder $builder) use ($genders) {
            $builder->whereHas(
                'profile',
                fn(Builder $b) => $b->whereIn('gender', $genders)
            );
        });
    }

    public function salary($salary = null)
    {
        $salaryRange = json_decode(htmlspecialchars_decode($salary), true);
        $this->builder->when($salary, function (Builder $builder) use ($salaryRange) {
            $builder->where(function (Builder $b) use ($salaryRange) {
                $b->whereHas(
                    'updatedSalary',
                    fn(Builder $builder) => $builder->whereBetween('amount', array_values($salaryRange))
                )->orWhereHas(
                    'salary',
                    fn(Builder $builder) => $builder->whereBetween('amount', array_values($salaryRange))
                );
            });
        });
    }

    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhereRaw(DB::raw('CONCAT(`first_name`, " ", `last_name`) LIKE ?'), ["%$search%"]);
            });
        });
    }

    public function contractType($contract_type = null)
    {
        $contract_types = $this->makeArray($contract_type);
        $this->builder->when($contract_type, function (Builder $builder) use ($contract_types) {
            $builder->whereHas(
                'provider',
                fn(Builder $b) => $b->whereIn('contract_type', $contract_types)
            );
        });
    }

    public function isGuardian($is_guardian=null){
        Log::info('waxx');
        $this->builder->when($is_guardian, function (Builder $builder) use ($is_guardian) {
            $builder->where('is_guardian', $is_guardian);
        });


    }

}
