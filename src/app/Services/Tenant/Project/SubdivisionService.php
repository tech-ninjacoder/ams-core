<?php

namespace App\Services\Tenant\Project;

use App\Models\Tenant\Project\Subdivision;
use App\Services\Tenant\TenantService;

class SubdivisionService extends TenantService
{
    protected $subdvisionId;

    public function __construct(Subdivision $subdivision )
    {
        $this->model = $subdivision;
    }


    public function setSubdivisionId($subdvisionId): SubdivisionService
    {
        $this->subdvisionId = $subdvisionId;
        return $this;
    }


    public function getSubdivisionId()
    {
        return $this->subdvisionId ?: $this->model->id;
    }

}
