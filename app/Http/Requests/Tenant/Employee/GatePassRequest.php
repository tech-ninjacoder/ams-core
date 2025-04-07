<?php

namespace App\Http\Requests\Tenant\Employee;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Employee\Helmet;

class GatePassRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules( new GatePass());
    }
}
