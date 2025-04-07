<?php

namespace App\Filters\Tenant;


use App\Filters\Core\traits\ImeiFilter;
use App\Filters\Core\traits\ImeiSearchFilter;
use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\DepartmentFilterTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class EmployeeAlertFilter  extends FilterBuilder
{
    use DateRangeFilter, SearchFilterTrait, NameFilter;
    public function search($search = null)
    {
        Log::info('alert');

        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('type', 'LIKE', "%$search%")
                    ->orWhere('note', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%$search%");
            });
        });
    }

    public function type($type = null)
    {
        Log::info('alert');

        return $this->builder->when($type, function (Builder $builder) use ($type) {
            $builder->where(function (Builder $builder) use ($type) {
                $builder->where('type', '=', $type)
                ->orWhere('note', 'LIKE',"%{$type}%")
                ;
            });
        });
    }


}
