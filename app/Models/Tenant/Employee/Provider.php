<?php

namespace App\Models\Tenant\Employee;


use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Employee\Relationship\ProviderRelationship;
use App\Models\Tenant\Employee\Rules\ProviderRules;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Traits\DepartmentRelationshipTrait;

class Provider extends TenantModel
{
    use StatusRelationship, DepartmentRelationshipTrait, ProviderRelationship , ProviderRules;

    protected $fillable = [
        'name', 'description', 'tenant_id', 'department_id','contract_type'
    ];
}
