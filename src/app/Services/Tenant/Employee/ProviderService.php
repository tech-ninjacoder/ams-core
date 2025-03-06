<?php

namespace App\Services\Tenant\Employee;

use App\Models\Tenant\Employee\Provider;
use App\Models\Tenant\Employee\ProviderUser;
use App\Services\Tenant\TenantService;

class ProviderService extends TenantService
{
    protected $providerId;

    public function __construct(Provider $provider )
    {
        $this->model = $provider;
    }

    public function assignToUsers($users)
    {
        $users = is_array($users) ? $users : func_get_args();

        $this->endPreviousProviderOfUsers($users);

        ProviderUser::insert(
            array_map(
                fn($user) => [
                    'user_id' => $user,
                    'start_date' => todayFromApp()->format('Y-m-d'),
                    'provider_id' => $this->getProviderId()
                ],
                ProviderUser::getNoneExistedUsers($this->getProviderId(), $users)
            )
        );
    }

    public function endPreviousProviderOfUsers($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        ProviderUser::whereIn('user_id', $users)
            ->whereNull('end_date')
            ->where('provider_id', '!=', $this->getProviderId())
            ->update([
                'end_date' => todayFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function setProviderId($providerId): ProviderService
    {
        $this->providerId = $providerId;
        return $this;
    }


    public function getProviderId()
    {
        return $this->providerId ?: $this->model->id;
    }

}
