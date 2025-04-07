<?php

namespace App\Models\Tenant\Project;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class Contractor extends TenantModel
{

    protected $fillable = [
        'name','phone_number', 'note'
    ];



}
