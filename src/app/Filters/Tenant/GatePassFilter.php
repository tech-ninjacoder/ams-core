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

class GatePassFilter  extends FilterBuilder
{
    use DateRangeFilter, SearchFilterTrait, NameFilter, DepartmentFilterTrait;

    public function valid($valid = null): void
    {
//        $has_helmet = $has_helmet;
//        $has_helmet =1;
        if($valid == 1) {
            Log::info('valid');

            $this->builder->whereDate('valid_from','<=',Carbon::today());
        } elseif ($valid == 1) {
            $this->builder->whereDate('valid_from','>',Carbon::yesterday());
        }



    }
    public function search($search = null)
    {
        return $this->builder->when($search, function (Builder $builder) use ($search) {
            $builder->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%$search%");
            });
        });
    }
}
