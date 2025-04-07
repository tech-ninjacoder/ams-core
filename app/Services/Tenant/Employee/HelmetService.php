<?php

namespace App\Services\Tenant\Employee;

use App\Models\Tenant\Employee\Helmet;
use App\Models\Tenant\Employee\HelmetUser;
use App\Services\Tenant\TenantService;

class HelmetService extends TenantService
{
    protected $helmetId;

    public function __construct(Helmet $helmet )
    {
        $this->model = $helmet;
    }

    public function assignToUsers($users)
    {
        $users = is_array($users) ? $users : func_get_args();

        $this->endPreviousHelmetOfUsers($users);

        HelmetUser::insert(
            array_map(
                fn($user) => [
                    'user_id' => $user,
                    'start_date' => todayFromApp()->format('Y-m-d'),
                    'helmet_id' => $this->getHelmetId()
                ],
                HelmetUser::getNoneExistedUsers($this->getHelmetId(), $users)
            )
        );
    }

    public function endPreviousHelmetOfUsers($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        HelmetUser::whereIn('user_id', $users)
            ->whereNull('end_date')
            ->where('helmet_id', '!=', $this->getHelmetId())
            ->update([
                'end_date' => todayFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function setHelmetId($helmetId): HelmetService
    {
        $this->helmetId = $helmetId;
        return $this;
    }


    public function getHelmetId()
    {
        return $this->helmetId ?: $this->model->id;
    }

}
