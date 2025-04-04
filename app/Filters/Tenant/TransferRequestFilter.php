<?php

namespace App\Filters\Tenant;


use App\Filters\Core\traits\NameFilter;
use App\Filters\Core\traits\SearchFilterTrait;
use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\DepartmentFilterTrait;
use Illuminate\Database\Eloquent\Builder;

class TransferRequestFilter  extends FilterBuilder
{
    use DateRangeFilter, SearchFilterTrait, NameFilter;
    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('title', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%$search%");
            });
        });
    }
}
