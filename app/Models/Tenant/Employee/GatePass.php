<?php

namespace App\Models\Tenant\Employee;


use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Employee\Relationship\GatePassRelationship;
use App\Models\Tenant\Employee\Relationship\HelmetRelationship;
use App\Models\Tenant\Employee\Rules\GatePassRules;
use App\Models\Tenant\Employee\Rules\HelmetRules;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Traits\DepartmentRelationshipTrait;

class GatePass extends TenantModel
{
    use StatusRelationship, DepartmentRelationshipTrait, GatePassRelationship , GatePassRules;

    protected $fillable = [
        'name', 'description', 'valid', 'gate_passe_type_id', 'valid_from', 'valid_to'
    ];
}
