<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\ProvidersFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\ProviderRequest;
use App\Models\Tenant\Employee\Provider;
use App\Services\Tenant\Employee\ProviderService;
use Illuminate\Support\Facades\Log;

class ProviderController extends Controller
{
    public function __construct(ProviderService $service, ProvidersFilter $filter)
    {
        Log::info(666);
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->withCount(['users' => function($query){
                $query
                    ->where('end_date',null)
                    ->where('is_in_employee',1);//get only number of active related user to the provider and is an employee
            } ])
            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );
    }

    public function store(ProviderRequest $request)
    {
        $this->service->save(
            $request->only('name', 'description','contract_type', 'tenant_id')
        );

        return created_responses('provider');
    }

    public function show(Provider $provider)
    {
        return $provider;
    }

    public function update(Provider $provider, ProviderRequest $request)
    {
        $provider->update(
            request()->only('name', 'description','contract_type', 'tenant_id')
        );

        return updated_responses('provider');
    }

    public function destroy(Provider $provider)
    {
        if ($provider->is_default) {
            throw new GeneralException(__t('action_not_allowed'));
        } elseif ($provider->users->count()) {
            throw new GeneralException(__t('cant_delete_provider'));
        }

        $provider->delete();

        return deleted_responses('provider');
    }

}
