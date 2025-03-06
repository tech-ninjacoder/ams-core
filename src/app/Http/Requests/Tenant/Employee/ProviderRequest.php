<?php

namespace App\Http\Requests\Tenant\Employee;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Employee\Provider;

class ProviderRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules( new Provider());
    }
}
