<?php


namespace App\Filters\Tenant;


use App\Helpers\Traits\UserAccessQueryHelper;
//use AWS\CRT\Log;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceDailLogFilter extends AttendanceRequestsFilter
{
    use UserAccessQueryHelper;

    public function showAll($showAll = 'yes')
    {
        $showAll = 'yes';
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
                $this->builder->when($showAll == 'no', function (Builder $builder) use ($engineer) {
                    Log::info('$showAll == no');
                    $builder->where('id', auth()->id());
                }, function (Builder $builder) use ($engineer){
                    Log::info('$showAll == yes '.request()->get('access_behavior'));

                    $builder->when(request()->get('access_behavior') == 'own_departments', function (Builder $b) use ($engineer)  {
                        return $b->when($engineer === true,
                            fn(Builder $b) => $this->projectAttendanceAccessQuery($b, 'user_id'),
                            fn(Builder $b) => $this->userAccessQuery($b, 'id')
                        );

                    });
                });
            }else{
                Log::info('Not project engineer');
                $this->builder->when($showAll == 'no', function (Builder $builder) {
                    $builder->where('user_id', auth()->id());
                },function (Builder $builder) {
                    $builder->when(request()->get('access_behavior') == 'own_departments',
                        fn(Builder $b) => $this->userAccessQuery($b)
                    );
                });
                $engineer = false;
            }
        }




    }
    public function projects ($project = null) {

        Log::info('test---'.$project);
        Log::info($project);
        Log::info(request()->get('date'));
        $date = request()->get('date');
        $date = Carbon::parse($date)->toDate();
//        return $this->builder->when($project, function (Builder $builder) use ($project) {
//            $builder->where(function (Builder $builder) use ($project) {
//                $builder->whereHas('details', fn(Builder $builder) => $builder->where('project_id','=', $project));
////                $builder->where('project_id', '=', $project);
//            });
//        })->tap(function ($query) {
//            // Log the SQL query and its bindings
//            $sql = $query->toSql();
//            $bindings = $query->getBindings();
//            Log::info('Builder SQL: ' . $sql);
//            Log::info('Builder Bindings: ' . json_encode($bindings));
//        });

        //this function will fetch employee that where assigned to selected project
        //need further work to make fetch employees where assigned on that specific date

        return $this->builder
            ->when($project, function (Builder $builder) use ($project, $date) {
                $builder->where(function (Builder $builder) use ($project, $date) {
                    // Filter attendance records by user's assignment to the project on the specific day
                    $builder->orWhereHas('user.assi_projects', function ($query) use ($project, $date) {
                        $query->where('project_id', $project)
                            ->whereDate('start_date', '<=', $date)
                            ->where(function ($query) use ($date) {
                                $query->where('end_date', '>=', $date)
                                    ->orWhereNull('end_date');
                            });
                    });
                })->orwhereHas('details', function ($query) use ($project, $date) {
                    $query->where('project_id', $project)
                        ->whereRaw('DATE(in_time) = ?', [$date]);
                });
            })->tap(function ($query) {
            // Log the SQL query and its bindings
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            Log::info('Builder SQL: ' . $sql);
            Log::info('Builder Bindings: ' . json_encode($bindings));

            });
    }
    public function status($status = null)
    {
        $this->builder->when($status , function (Builder $builder) use ($status) {
            $builder->where('status_id', $status);
        });
    }
}
