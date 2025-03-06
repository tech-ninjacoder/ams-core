<?php

namespace App\Http\Controllers\Tenant\Project;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\ContractorsFilter;
use App\Filters\Tenant\DesignationsFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Project\ContractorRequest;
use App\Http\Requests\Tenant\Employee\DesignationRequest;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Project\Contractor;
use App\Services\Tenant\Employee\DesignationService;
use App\Services\Tenant\Project\ContractorService;

class ContractorController extends Controller
{
    public function __construct(ContractorService $service, ContractorsFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );
    }

    public function index_api(){
        $contractors = Contractor::select('id','name')->get();
        return $contractors->toJson();
    }

    public function store(ContractorRequest $request)
    {
        $this->service->save(
            $request->only('name', 'phone_number', 'note')
        );

        return created_responses('contractor');
    }

    public function show(Contractor $contractor)
    {
        return $contractor;
    }

    public function update(Contractor $contractor, ContractorRequest $request)
    {
        $contractor->update(
            request()->only('name', 'phone_number', 'note')
        );

        return updated_responses('contractor');
    }

    public function destroy(Contractor $contractor)
    {

        $contractor->delete();

        return deleted_responses('contractor');
    }

}
