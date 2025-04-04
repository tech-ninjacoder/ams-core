<?php

namespace App\Http\Requests\Tenant\Project;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Project\Location;

class LocationRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules( new Location());
    }
}
