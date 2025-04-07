<?php

namespace App\Services\Tenant\Employee;

use App\Models\Tenant\Employee\HelmetUser;
use App\Models\Tenant\Employee\Skill;
use App\Models\Tenant\Employee\SkillUser;
use App\Services\Tenant\TenantService;

class SkillService extends TenantService
{
    protected $skillId;

    public function __construct(Skill $skill )
    {
        $this->model = $skill;
    }

    public function assignToUsers($users)
    {
        $users = is_array($users) ? $users : func_get_args();

        $this->endPreviousSkillOfUsers($users);

        SkillUser::insert(
            array_map(
                fn($user) => [
                    'user_id' => $user,
                    'start_date' => todayFromApp()->format('Y-m-d'),
                    'skill_id' => $this->getSkillId()
                ],
                SkillUser::getNoneExistedUsers($this->getSkillId(), $users)
            )
        );
    }

    public function endPreviousSkillOfUsers($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        HelmetUser::whereIn('user_id', $users)
            ->whereNull('end_date')
            ->where('skill_id', '!=', $this->getSkillId())
            ->update([
                'end_date' => todayFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function setSkillId($skillId): SkillService
    {
        $this->skillId = $skillId;
        return $this;
    }


    public function getSkillId()
    {
        return $this->skillId ?: $this->model->id;
    }

}
