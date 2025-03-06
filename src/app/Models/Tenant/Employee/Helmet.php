<?php

namespace App\Models\Tenant\Employee;


use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Employee\Relationship\HelmetRelationship;
use App\Models\Tenant\Employee\Rules\HelmetRules;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Traits\DepartmentRelationshipTrait;

class Helmet extends TenantModel
{
    use StatusRelationship, DepartmentRelationshipTrait, HelmetRelationship , HelmetRules;

    protected $fillable = [
        'imei','pme_barcode', 'description', 'tenant_id', 'department_id'
    ];
    protected $casts = ['pme_barcode' => 'string'];
}
