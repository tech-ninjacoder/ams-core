<?php


namespace App\Models\Tenant\Employee\Relationship;



use App\Models\Tenant\Employee\WorkersProviders;
use App\Models\Tenant\Traits\WorkersProvidersRelationshipTrait;


trait WorkersProvidersRelationship
{
    use WorkersProvidersRelationshipTrait;

    public function workerproviderUser()
    {
        return $this->belongsTo(WorkersProviders::class, 'work_provider_id', 'id');
    }
}
