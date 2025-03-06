<?php

namespace App\Http\Requests\Tenant\Request;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Request\TransferRequest;

class TransferHttpRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules( new TransferRequest());
    }
}
