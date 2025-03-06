<?php

namespace Database\Seeders\Auth;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use Illuminate\Database\Seeder;
use App\Models\Core\Auth\Type;
use Illuminate\Support\Facades\Log;

class AmsPermissionsRolesSeeder extends Seeder
{
    //php artisan db:seed --class=Database\\Seeders\\Auth\\AmsPermissionsRolesSeeder
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!optional(tenant())->is_single) {
            return;
        }

        // Create new permissions
        $newPermissions = [
            'access_own_projects',
            'access_own_project_employees',
            'access_own_project_coordinator',
            'create_projects',
            'view_projects',
            'update_projects',
            'delete_projects',
            'view_contractors',
            'update_contractors',
            'delete_contractors',
            'create_contractors',
            'view_locations',
            'update_locations',
            'delete_locations',
            'create_locations',
            'view_subdivisions',
            'update_subdivisions',
            'delete_subdivisions',
            'create_subdivisions',
            'add_employees_to_working_project',
            'add_employees_to_project',
            'add_gate_pass_project',
        ];

        // Create new permissions
        $newProviderEmployeePermissions = [
            'create_providers',
            'view_providers',
            'update_providers',
            'delete_providers',
        ];

        // Create new permissions
        $newHelmetPermissions = [
            'create_helmets',
            'view_helmets',
            'update_helmets',
            'delete_helmets',
        ];

        // Create new permissions
        $newGatePassesPermissions = [
            'create_gate_passes',
            'view_gate_passes',
            'update_gate_passes',
            'delete_gate_passes',
        ];
        //view_skills
        $newSkillsPermissions = [
            'create_skills',
            'view_skills',
            'update_skills',
            'delete_skills',
        ];
        //excel_attendance_download
        $newAttendancePermissions = [
            'excel_attendance_download',
            'synch_attendance',
            'bulk_approve_attendance',
            'bulk_correct_attendance',
            'bulk_clear_correction_attendance'
            ];

        $typeId = Type::findByAlias('tenant')->id;

        foreach ($newPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'type_id' => $typeId],
                ['group_name' => 'project']
            );
        }

        foreach ($newProviderEmployeePermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'type_id' => $typeId],
                ['group_name' => 'employees']
            );
        }

        foreach ($newHelmetPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'type_id' => $typeId],
                ['group_name' => 'helmets']
            );
        }

        foreach ($newGatePassesPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'type_id' => $typeId],
                ['group_name' => 'gate_passes']
            );
        }

        foreach ($newSkillsPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'type_id' => $typeId],
                ['group_name' => 'skills']
            );
        }
        foreach ($newAttendancePermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'type_id' => $typeId],
                ['group_name' => 'attendance']
            );
        }

        // Create new roles and assign all permissions
        $roles = [
            'Project Engineer',
            'Coordinator',
            'Camp Boss',
            'Camp Boss Plus',
            'Store',
            'QS',
            'Site Application Manager',
            'Quality control',
            'Cost Control'
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'type_id' => $typeId],
                [
                    'is_default' => 1,
                    'alias' => strtolower(str_replace(' ', '_', $roleName))
                ]
            );

            // Uncomment the following lines if you want to assign all permissions to each role
            // $role->permissions()->sync(
            //     Permission::query()->pluck('id')->toArray()
            // );

            // First, update the name where alias is 'department_manager'
            Role::where('alias', 'department_manager')
                ->update(['name' => 'Project Manager']);

            // Assign specific permissions to the department_manager role
            $departmentManagerRole = Role::where([
                'name' => 'Project Manager',
                'type_id' => $typeId
            ])->first();


            $permissionsToAssign = [
                'create_projects',
                'view_projects',
                'update_projects',
                'view_contractors',
                'view_locations',
                'view_subdivisions',
                'add_employees_to_working_project',
                'add_employees_to_project',
                'add_gate_pass_project',
                'view_providers',
                'view_helmets',
                'view_gate_passes',
                'view_skills',
                'excel_attendance_download',
                'bulk_approve_attendance',
                'bulk_correct_attendance',
                'bulk_clear_correction_attendance'


            ];

            $permissions = Permission::whereIn('name', $permissionsToAssign)
                ->where('type_id', $typeId)
                ->pluck('id')
                ->toArray();
            Log::info('permissions====> '.json_encode($permissions));

            $departmentManagerRole->permissions()->syncWithoutDetaching($permissions);
        }
    }
}
