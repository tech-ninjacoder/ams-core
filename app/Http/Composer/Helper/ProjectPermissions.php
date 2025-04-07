<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class ProjectPermissions
{
    use InstanceCreator;

    public function permissions()
    {
        return [
            [
                'name' => __t('projects'),
                'url' => $this->projectUrl(),
                'permission' => authorize_any(['access_own_projects','access_own_departments'])
            ],
            [
                'name' => __t('contractors'),
                'url' => $this->ContractorUrl(),
                'permission' => authorize_any(['access_own_projects','access_own_departments'])
            ],
            [
                'name' => __t('locations'),
                'url' => $this->LocationUrl(),
                'permission' => authorize_any(['access_own_projects','access_own_departments'])
            ],
            [
                'name' => __t('subdivisions'),
                'url' => $this->SubdivisionUrl(),
                'permission' => authorize_any(['access_own_projects','access_own_departments'])
            ]

        ];
    }

    public function canVisit()
    {
        return authorize_any(['view_employees', 'view_designations', 'view_employment_statuses','access_own_projects','access_own_departments']);
    }

    public function projectUrl()
    {
        return route(
            'support.employee.projects',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function ContractorUrl()
    {
        return route(
            'support.projects.contractors',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
    public function LocationUrl()
    {
        return route(
            'support.projects.locations',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
    public function SubdivisionUrl()
    {
        return route(
            'support.projects.subdivisions',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
}
