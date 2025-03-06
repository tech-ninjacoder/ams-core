<?php

namespace App\Http\Requests\Tenant\Employee;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\Skill;

class SkillRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules( new Skill());
    }
}
