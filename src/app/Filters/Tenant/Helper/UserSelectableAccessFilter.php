<?php


namespace App\Filters\Tenant\Helper;


use App\Filters\FilterBuilder;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class UserSelectableAccessFilter extends FilterBuilder
{
    use UserAccessQueryHelper;

//    public function showAll($showAll = 'yes')
//    {
//        $this->builder->when($showAll == 'no', function (Builder $builder) {
//            $builder->where('id', auth()->id());
//        },function (Builder $builder) {
//            $builder->when(request()->get('access_behavior') == 'own_departments',
//                fn(Builder $b) => $this->userAccessQuery($b, 'id', request()->has('with_auth'))
//            );
//        });
//    }
    public function showAll($showAll = 'yes')
    {
        $user = auth()->user();
        $user_role = $user->load('roles');
        Log::info('EmployeeFilter.php showAll');

        Log::info('$user_role '.$user_role->roles);
        $engineer = null;
        foreach ($user_role->roles as $role) {
            Log::info('role=>' . $role);
            if ($role->alias === 'project_engineer') {
                Log::info('project engineer');
                $engineer = true;
            } else {
                Log::info('Not project engineer');
                $engineer = false;
            }
        }

        $this->builder->when($showAll == 'no', function (Builder $builder) {
            $builder->where('id', auth()->id());
        }, function (Builder $builder) use ($engineer) {
            $builder->when(request()->get('access_behavior') == 'own_departments', function (Builder $b) use ($engineer) {
                return $b->when($engineer,
                    fn(Builder $b) => $this->enginnerAccessQuery($b, 'id', request()->has('with_auth')),
                    fn(Builder $b) => $this->userAccessQuery($b, 'id', request()->has('with_auth'))
                );
            });
        });
    }

}