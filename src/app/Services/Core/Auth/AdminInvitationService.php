<?php


namespace App\Services\Core\Auth;


use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\Core\BaseService;

class AdminInvitationService extends BaseService
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function invite($email,$employee_id,$password,$first_name,$last_name, $roles = [])
    {
        $roles = count($roles) ? $roles : request()->get('roles');

        $this->create($email,$employee_id,$password, $first_name,$last_name)->assignRoles($roles);

        $this->model->addAdmin();

        return $this->model;
    }

    public function create($email,$employee_id,$password, $first_name, $last_name, array $attributes = [])
    {
        $status = Status::findByNameAndType('status_active')->id;
        $fake_email = $employee_id.'@pmeams.com';

        $invitation_token = base64_encode($email.'-invitation-from-us');

        $this->model->fill(array_merge([
                'email' => $email,
                'password'=> $password,
                'status_id' => $status,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'invitation_token' => $invitation_token
            ], $attributes))->save();

        return $this;
    }

    public function assignRoles($roles)
    {
        foreach ($roles as $key => $role) {
            $this->model->assignRole($role);
        }

        return $this;
    }
    public function assignGatePasses($gatepasses)
    {
        foreach ($gatepasses as $key => $gatepass) {
            $this->model->assignGatePass($gatepass);
        }

        return $this;
    }
    public function assignToSkills($skills)
    {
        foreach ($skills as $key => $skill) {
            $this->model->assignSkill($skill);
        }

        return $this;
    }

    public function detachRoles()
    {
        $this->model->roles()->sync([]);

        return $this;
    }

    public function delete()
    {
        $this->model->forceDelete();

        return true;
    }
}
