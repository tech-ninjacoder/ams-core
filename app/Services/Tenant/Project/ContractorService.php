<?php

namespace App\Services\Tenant\Project;

use App\Models\Tenant\Project\Contractor;
use App\Services\Tenant\TenantService;

class ContractorService extends TenantService
{
    protected $contractorId;

    public function __construct(Contractor $contractor )
    {
        $this->model = $contractor;
    }

    public function assignToUsers($users)
    {
        $users = is_array($users) ? $users : func_get_args();

        $this->endPreviousContractorOfUsers($users);

        ContractorUser::insert(
            array_map(
                fn($user) => [
                    'user_id' => $user,
                    'start_date' => todayFromApp()->format('Y-m-d'),
                    'Contractor_id' => $this->getContractorId()
                ],
                ContractorUser::getNoneExistedUsers($this->getContractorId(), $users)
            )
        );
    }

    public function endPreviousContractorOfUsers($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        ContractorUser::whereIn('user_id', $users)
            ->whereNull('end_date')
            ->where('Contractor_id', '!=', $this->getContractorId())
            ->update([
                'end_date' => todayFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function setContractorId($contractorId): ContractorService
    {
        $this->ContractorId = $contractorId;
        return $this;
    }


    public function getContractorId()
    {
        return $this->ContractorId ?: $this->model->id;
    }

}
