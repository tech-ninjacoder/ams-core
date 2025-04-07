<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class AdministrationPermissions
{
    use InstanceCreator;

    public function permissions()
    {
        return [
            [
                'name' => __t('users_roles'),
                'url' => $this->userUrl(),
                'permission' => authorize_any(['view_roles'])
            ],
            [
                'name' => __t('work_shifts'),
                'url' => $this->workShiftUrl(),
                'permission' => authorize_any(['view_working_shifts'])
            ],
//            [
//                'name' => __t('projects'),
//                'url' => $this->projectUrl(),
//                'permission' => authorize_any(['view_working_shifts'])
//            ],
            [
                'name' => __t('departments'),
                'url' => $this->departmentUrl(),
                'permission' => authorize_any(['view_departments'])
            ],
            [
                'name' => __t('skills'),
                'url' => $this->SkillUrl(),
                'permission' => authorize_any(['view_departments'])
            ],
            [
                'name' => __t('workers_providers'),
                'url' => $this->workers_providersUrl(),
                'permission' => authorize_any(['view_departments'])
            ],
            [
                'name' => __t('holiday'),
                'url' => $this->holidayUrl(),
                'permission' => authorize_any(['view_holidays'])
            ],
            [
                'name' => __t('transfer_request'),
                'url' => $this->transfer_requestUrl(),
                'permission' => authorize_any(['view_designation'])
            ],
//            [
//                'name' => __t('org_structure'),
//                'url' => $this->organizationUrl(),
//                'permission' => auth()->user()->can('view_departments') &&
//                    auth()->user()->can('update_departments')
//            ],
        ];
    }

    public function canVisit()
    {
        return authorize_any(['view_users', 'view_roles', 'view_departments', 'view_working_shifts']);
    }

    public function departmentUrl()
    {
        return route(
            'support.employee.departments',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
    public function workers_providersUrl()
    {
        return route(
            'support.employee.workers_providers',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function SkillUrl()
    {
        return route(
            'support.employee.skills',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function HelmetUrl()
    {
        return route(
            'support.employee.helmets',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
    public function AlertUrl()
    {
        return route(
            'support.employee.helmets',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function organizationUrl()
    {
        return route(
            'support.organization.structure',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function workShiftUrl()
    {
        return route(
            'support.employee.work_shifts',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
    public function projectUrl()
    {
        return route(
            'support.employee.projects',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function userUrl()
    {
        return route(
            'support.tenant.users',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function holidayUrl()
    {
        return route(
            'support.employee.holidays',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
    public function transfer_requestUrl()
    {
        return route(
            'support.transfer_request.all',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
}
