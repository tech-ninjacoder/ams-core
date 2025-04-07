<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class EmployeePermissions
{
    use InstanceCreator;

    public function permissions()
    {
        return [
            [
                'name' => __t('all_employees'),
                'url' => route('support.employee.lists',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => true
            ],
            [
                'name' => __t('designation'),
                'url' => route('support.employee.designations',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_designations'])
            ],
            [
                'name' => __t('provider'),
                'url' => route('support.employee.providers',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_designations'])
            ],
            [
                'name' => __t('helmet'),
                'url' => route('support.employee.helmets',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_designations'])
            ],
            [
                'name' => __t('gate_passes'),
                'url' => route('support.employee.gate_passes',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_designations'])
            ],
            [
                'name' => __t('employment_status'),
                'url' => route('support.employee.employment-statuses',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_employment_statuses'])
            ],
            [
                'name' => __t('transfer_request'),
                'url' => route('support.transfer_request.all',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_designations'])
            ],
            [
                'name' => __t('alerts'),
                'url' => route('support.employee.alerts',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_alerts'])
            ],
        ];
    }

    public function canVisit()
    {
        return authorize_any(['view_employees', 'view_designations', 'view_employment_statuses']);
    }

    public function profile($user = null)
    {
        return route(
            'support.employee.details',
            !optional(tenant())->is_single ?
            [
                'employee' => $user ?: auth()->id(),
                'tenant_parameter' => optional(tenant())->short_name ?: 'default-tenant'
            ]: [
                'employee' => $user ?: auth()->id()
            ]
        );
    }
}
