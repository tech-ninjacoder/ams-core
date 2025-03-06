<?php

namespace App\Models\Tenant\Employee;


use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Employee\Relationship\ProviderRelationship;
use App\Models\Tenant\Employee\Relationship\SkillRelationship;
use App\Models\Tenant\Employee\Rules\ProviderRules;
use App\Models\Tenant\Employee\Rules\SkillRules;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Traits\DepartmentRelationshipTrait;

class Skill extends TenantModel
{
    use StatusRelationship, SkillRelationship , SkillRules;

    protected $fillable = [
        'name', 'description'
    ];
}
