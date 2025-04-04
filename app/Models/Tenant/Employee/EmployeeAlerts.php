<?php

namespace App\Models\Tenant\Employee;

use App\Models\Core\Auth\User;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Employee\Relationship\AlertRelationship;
use App\Models\Tenant\Employee\Relationship\HelmetRelationship;
use App\Models\Tenant\Employee\Rules\HelmetRules;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Traits\DepartmentRelationshipTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAlerts extends TenantModel
{
    use HasFactory;
    use SoftDeletes;
    use AlertRelationship;

    protected $table = 'alerts';
    protected $fillable = [
        'type', 'user_id', 'date', 'note'
    ];

}
