<?php

namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\DepartmentFilterTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class LocationFilter  extends FilterBuilder
{
    use DateRangeFilter, SearchFilterTrait, NameFilter, DepartmentFilterTrait;
    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'LIKE', "%$search%")
                    ->orWhere('id', 'LIKE', "%$search%");
            });
        });
    }
}
