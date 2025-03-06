<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\GatePassFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\GatePassRequest;
use App\Models\Tenant\Employee\GatePass;
use App\Services\Tenant\Employee\GatePassService;

class GatePassController extends Controller
{
    public function __construct(GatePassService $service, GatePassFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->withCount(['users' => function($query){
                $query
                    ->where('end_date',null);
//                    ->where('is_in_employee',1);//get only number of active related user to the provider and is an employee
            } ])
            ->latest()
            ->paginate(
                request()->get('per_page', 10)
            );
    }

    public function store(GatePassRequest $request)
    {
        $this->service->save(
            $request->only('name', 'description', 'valid', 'gate_passe_type_id', 'valid_from','valid_to')
        );

        return created_responses('GatePass');
    }

    public function show(GatePass $gatePass)
    {
        return $gatePass;
    }

    public function update(GatePass $gatePass, GatePassRequest $request)
    {
        $gatePass->update(
            request()->only('name', 'description', 'valid','gate_passe_type_id','valid_from','valid_to')
        );

        return updated_responses('GatePass');
    }

    public function destroy(GatePass $gatePass)
    {
      if ($gatePass->users->count()) {
            throw new GeneralException(__t('cant_delete_gate_pass'));
        }

        $gatePass->delete();

        return deleted_responses('GatePass');
    }




    }
