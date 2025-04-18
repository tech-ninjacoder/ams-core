<?php


namespace App\Filters\Tenant;


use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\StatusFilterTrait;
use App\Helpers\Traits\MakeArrayFromString;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use Illuminate\Database\Eloquent\Builder;

class WorkersProvidersFilter extends FilterBuilder
{
    use DateRangeFilter, StatusFilterTrait;
    use MakeArrayFromString;

    public function exclude($exclude = null)
    {
        $excluded = $this->makeArray($exclude);

        $this->builder->when(
            count($excluded),
            fn(Builder $builder) => $builder->whereNotIn('id', $excluded)
        );
    }


    public function search($value = null)
    {
        $this->builder->when($value, function (Builder $builder) use ($value) {
            $builder->where('name', "LIKE", "%{$value}%")
                ->orWhere(fn(Builder $b) => $b->whereHas(
                    'employee',
                    fn(Builder $bb) => $bb->where('first_name', "LIKE", "%{$value}%")
                        ->orWhere('last_name', "LIKE", "%{$value}%")
                        ->orWhereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE '%$value%'")
                ));
        });
    }
}
