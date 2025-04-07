<?php


namespace App\Models\Tenant\Traits;


use App\Models\Tenant\Employee\WorkersProviders;

trait WorkersProvidersRelationshipTrait
{
    public function employee()
    {
        return $this->belongsTo(WorkersProviders::class);
    }
}
