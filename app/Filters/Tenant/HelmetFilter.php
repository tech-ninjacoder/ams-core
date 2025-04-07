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

class HelmetFilter  extends FilterBuilder
{
    use DateRangeFilter, ImeiSearchFilter, ImeiFilter, DepartmentFilterTrait;
    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('imei', 'LIKE', "%$search%")
                    ->orWhere('pme_barcode', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        });
    }
}
