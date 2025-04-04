<?php


namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\StatusFilterTrait;
use App\Helpers\Traits\MakeArrayFromString;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectFilter extends FilterBuilder
{
    use DateRangeFilter, StatusFilterTrait, SearchFilterTrait, NameFilter, MakeArrayFromString;

    public function type($type = '')
    {
        $types = $this->makeArray($type);
        Log::info('type '.$type);
        $this->builder->when($type && count($types), function (Builder $builder) use ($type) {
            if ($type == 1) {
                $builder->whereHas('parent');
            } elseif($type == 2) {
                $builder->whereHas('childrens');
            }elseif($type == 3) {
                $builder->whereHas('parent')->whereHas('childrens');
            }elseif ($type == 4){
                $builder->whereDoesntHave('parent')->whereDoesntHave('childrens');
            }
        });
    }

    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'LIKE', "%$search%")
                    ->orWhere('pme_id', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%$search%");
            });
        });
    }
    public function contractors($users = null): void
    {
        $users = $this->makeArray($users);

        $this->builder->when(count($users), function (Builder $builder) use ($users) {
            $builder->whereHas(
                'contractors', fn(Builder $builder) => $builder->whereIn('id', $users)
            );
        });
    }
    public function locations($users = null): void
    {
        $users = $this->makeArray($users);

        $this->builder->when(count($users), function (Builder $builder) use ($users) {
            $builder->whereHas(
                'locations', fn(Builder $builder) => $builder->whereIn('id', $users)
            );
        });
    }
    public function subdivisions($users = null): void
    {
        $users = $this->makeArray($users);

        $this->builder->when(count($users), function (Builder $builder) use ($users) {
            $builder->whereHas(
                'subdivisions', fn(Builder $builder) => $builder->whereIn('id', $users)
            );
        });
    }
    public function status($users = null): void
    {
        $users = $this->makeArray($users);

        $this->builder->when(count($users), function (Builder $builder) use ($users) {
            $builder->whereHas(
                'status', fn(Builder $builder) => $builder->whereIn('id', $users)
            );
        });
    }
//    public function accessBehavior($accessBehavior = 'own_projects')
//    {
//        $this->builder->when($accessBehavior == 'own_projects', function (Builder $builder) {
//            $adminProjects = resolve(DepartmentRepository::class)->getDepartments(auth()->id());
//
//            $builder->whereIn('id', $userDepartments);
//        });
//    }
//    public function working_shifts($working_shifts = null): void
//    {
//        $working_shifts = $this->makeArray($working_shifts);
//
//        $this->builder->when(count($working_shifts), function (Builder $builder) use ($working_shifts) {
//            $builder->whereHas(
//                'working_shifts', fn(Builder $builder) => $builder->whereIn('id', $working_shifts)
//            );
//        });
//    }

}
