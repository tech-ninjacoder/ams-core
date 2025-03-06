<?php

namespace App\Http\Requests\Tenant\Project;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Project\Contractor;

class ContractorRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules( new Contractor());
    }
}
