<?php

namespace Database\Seeders\Auth;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use Illuminate\Database\Seeder;

class AssignAllPermissionsToRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find the role with ID 1
        $role = Role::find(1);

        if ($role) {
            // Assign all permissions to this role
            $permissions = Permission::all()->pluck('id')->toArray();
            $role->permissions()->sync($permissions);
        }
    }

}
