<?php
namespace App\Models\Tenant\Employee;

use App\Models\Core\Auth\Profile as BaseProfile;
use App\Models\Tenant\Employee\Relationship\WorkersProvidersRelationship;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Traits\ProfileRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
class WorkersProviders extends TenantModel
{
    use WorkersProvidersRelationship;
    protected $fillable = [
        'name', 'description'
    ];

}
