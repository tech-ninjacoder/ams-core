<?php

namespace App\Services\Tenant\Employee;

use App\Models\Tenant\Employee\GatePass;
use App\Models\Tenant\Employee\GatePassUser;
use App\Services\Tenant\TenantService;

class GatePassService extends TenantService
{
    protected $gatePassId;

    public function __construct(GatePass $gatepass )
    {
        $this->model = $gatepass;
    }

    public function assignToUsers($users)
    {
        $users = is_array($users) ? $users : func_get_args();

        $this->endPreviousGatePassOfUsers($users);

        GatePassUser::insert(
            array_map(
                fn($user) => [
                    'user_id' => $user,
                    'start_date' => todayFromApp()->format('Y-m-d'),
                    'gate_pass_id' => $this->getGatePassId()
                ],
                GatePassUser::getNoneExistedUsers($this->getGatePassId(), $users)
            )
        );
    }

    public function endPreviousGatePassOfUsers($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        GatePassUser::whereIn('user_id', $users)
            ->whereNull('end_date')
            ->where('gate_pass_id', '!=', $this->getGatePassId())
            ->update([
                'end_date' => todayFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function setGatePassId($gatePassId): GatePassService
    {
        $this->gatePassId = $gatePassId;
        return $this;
    }


    public function getGatePassId()
    {
        return $this->gatePassId ?: $this->model->id;
    }

}
